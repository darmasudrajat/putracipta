<?php

namespace App\Command\Crud;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'custom:clear:crud',
    description: 'Clear CRUD files'
)]
class ClearCrudCommand extends Command
{
    private string $projectDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('entity-class', InputArgument::REQUIRED, 'The class name of the entity to clear CRUD');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $classVars = $this->retrieveClassVars($input->getArgument('entity-class'));
        $fileRef = $this->getFileRef($classVars);

        $this->writeFilenamesToDelete($output, $fileRef);
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(' <info>Are you sure you want to delete these files? (yes/no)</info> [<comment>yes</comment>] ', true);
        if (!$helper->ask($input, $output, $question)) {
            return Command::FAILURE;
        }
        $this->removeExistingCrudFiles($fileRef);
        $output->writeln('');
        $output->writeln(' <info>The files have been successfully deleted</info>: ' . $classVars['entityFullName']);

        return Command::SUCCESS;
    }

    private function writeFilenamesToDelete(OutputInterface $output, array $fileRef): void
    {
        $filenames = array_map(fn($destination) => implode('/', $destination), array_values($fileRef));
        foreach ($filenames as $filename) {
            $output->writeln(' ' . $filename);
        }
        $output->writeln('');
    }

    private function removeExistingCrudFiles(array $fileRef): void
    {
        foreach ($fileRef as $destination) {
            $file = $this->projectDir . '/' . implode('/', $destination);
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    private function getFileRef(array $classVars): array
    {
        $entityPath = str_replace('\\', '/', $classVars['entityNamespace']);
        $commandTemplateDir = __DIR__ . '/templates';
        $entityControllerPath = 'src/Controller';
        $entityFormPath = 'src/Form';
        $entityGridPath = 'src/Grid';
        if ($entityPath !== '') {
            $entityControllerPath .= "/{$entityPath}";
            $entityFormPath .= "/{$entityPath}";
            $entityGridPath .= "/{$entityPath}";
        }
        $entityTemplatePath = "templates/{$classVars['templatePathPrefix']}";
        $fileRef = [
            "{$commandTemplateDir}/Controller.tpl.php" => [$entityControllerPath, "{$classVars['entityName']}Controller.php"],
            "{$commandTemplateDir}/Form.tpl.php" => [$entityFormPath, "{$classVars['entityName']}Type.php"],
            "{$commandTemplateDir}/Grid.tpl.php" => [$entityGridPath, "{$classVars['entityName']}GridType.php"],
            "{$commandTemplateDir}/_delete_form.tpl.php" => [$entityTemplatePath, '_delete_form.html.twig'],
            "{$commandTemplateDir}/_form.tpl.php" => [$entityTemplatePath, '_form.html.twig'],
            "{$commandTemplateDir}/_list.tpl.php" => [$entityTemplatePath, '_list.html.twig'],
            "{$commandTemplateDir}/edit.tpl.php" => [$entityTemplatePath, 'edit.html.twig'],
            "{$commandTemplateDir}/index.tpl.php" => [$entityTemplatePath, 'index.html.twig'],
            "{$commandTemplateDir}/new.tpl.php" => [$entityTemplatePath, 'new.html.twig'],
            "{$commandTemplateDir}/show.tpl.php" => [$entityTemplatePath, 'show.html.twig'],
        ];

        return $fileRef;
    }

    private function retrieveClassVars(string $entityClass): array
    {
        $vars = [];

        $vars['entityFullName'] = $entityClass;
        $matches = [];
        if (preg_match('/(.+)\\\\(.+?)$/', $vars['entityFullName'], $matches)) {
            $vars['entityNamespace'] = $matches[1];
            $vars['entityName'] = $matches[2];
        } else {
            $vars['entityNamespace'] = '';
            $vars['entityName'] = $vars['entityFullName'];
        }
        $vars['entityTitle'] = preg_replace('/([a-z])([A-Z])/s','$1 $2', $vars['entityName']);
        $vars['templatePathPrefix'] = strtolower(preg_replace('/(?<!^|\/)([A-Z])/', '_$1', str_replace('\\', '/', $vars['entityFullName'])));

        return $vars;
    }
}
