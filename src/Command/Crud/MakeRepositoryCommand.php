<?php

namespace App\Command\Crud;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'custom:make:repository',
    description: 'Make custom repository file'
)]
class MakeRepositoryCommand extends Command
{
    private string $projectDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('entity-class', InputArgument::REQUIRED, 'The class name of the entity to make repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $classVars = $this->retrieveClassVars($input->getArgument('entity-class'));
        $this->createNewRepositoryFile($classVars);
        $output->writeln(' <info>The repository file has been successfully created</info>: ' . $classVars['entityFullName']);

        return Command::SUCCESS;
    }

    private function createNewRepositoryFile(array $classVars): void
    {
        $entityPath = str_replace('\\', '/', $classVars['entityNamespace']);
        $commandTemplateDir = __DIR__ . '/templates';
        $entityRepositoryPath = 'src/Repository';
        if ($entityPath !== '') {
            $entityRepositoryPath .= "/{$entityPath}";
        }
        $source = "{$commandTemplateDir}/Repository.tpl.php";
        $destination = [$entityRepositoryPath, "{$classVars['entityName']}Repository.php"];

        $vars = $classVars;
        ob_start();
        include($source);
        $contents = ob_get_contents();
        ob_end_clean();
        $dir = $this->projectDir . '/' . $destination[0];
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($this->projectDir . '/' . implode('/', $destination), $contents);
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

        return $vars;
    }
}
