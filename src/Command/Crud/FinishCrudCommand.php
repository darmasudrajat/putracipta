<?php

namespace App\Command\Crud;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'custom:finish:crud',
    description: 'Finish custom CRUD files'
)]
class FinishCrudCommand extends Command
{
    private string $projectDir;
    private EntityManagerInterface $entityManager;

    public function __construct(KernelInterface $kernel, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $kernel->getProjectDir();
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('entity-class', InputArgument::REQUIRED, 'The class name of the entity to finish CRUD');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $classVars = $this->retrieveClassVars($input->getArgument('entity-class'));
        $fileRef = $this->getFileRef($classVars);
        $fileVars = $this->retrieveFileVars($classVars);
        $this->createNewCrudFiles($fileRef, $classVars, $fileVars);
        $output->writeln(' <info>The CRUD files have been successfully completed</info>: ' . $classVars['entityFullName']);

        return Command::SUCCESS;
    }

    private function createNewCrudFiles(array $fileRef, array $classVars, array $fileVars): void
    {
        $vars = array_merge($classVars, $fileVars);
        foreach ($fileRef as $source => $destination) {
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

    private function retrieveFileVars(array $classVars): array
    {
        $vars = [];

        foreach (file($this->projectDir . '/src/Controller/' . str_replace('\\', '/', $classVars['entityFullName']) . 'Controller.php') as $line) {
            $matches = [];
            if (preg_match('/name: \'(.+)_index\'/', $line, $matches)) {
                $vars['routeNamePrefix'] = $matches[1];
            } else if (preg_match('/\$(.+) = new/', $line, $matches)) {
                $vars['instanceNameSingular'] = $matches[1];
            } else if (preg_match('/\'(.+)\'.+findAll/', $line, $matches)) {
                $vars['instanceNamePlural'] = str_replace('_', '', lcfirst(ucwords($matches[1], '_')));
            }
        }
        foreach (file($this->projectDir . '/src/Form/' . str_replace('\\', '/', $classVars['entityFullName']) . 'Type.php') as $line) {
            $matches = [];
            if (preg_match('/add\(\'(.+)\'\)/', $line, $matches)) {
                $vars['formFields'][] = $matches[1];
            }
        }
        foreach (file($this->projectDir . "/templates/{$classVars['templatePathPrefix']}/index.html.twig") as $line) {
            $matches = [];
            if (preg_match('/<th>(.+)<\/th>/', $line, $matches)) {
                if ($matches[1] !== 'Id' && $matches[1] !== 'actions') {
                    $vars['dataHeaders'][] =  preg_replace('/([a-z])([A-Z])/s','$1 $2', $matches[1]);
                }
            } else if (preg_match('/<td>{{ (.+) }}<\/td>/', $line, $matches)) {
                $templatePathParts = explode('/', $classVars['templatePathPrefix']);
                $instanceName = $templatePathParts[array_key_last($templatePathParts)];
                if ($matches[1] !== "{$instanceName}.id") {
                    $fieldValue = str_replace($instanceName, $vars['instanceNameSingular'], $matches[1]);
                    $vars['dataFieldValues'][] = $fieldValue;
                    $valueCharPos = strpos($fieldValue, ' ');
                    $fieldFullName = $valueCharPos !== false ? str_replace(substr($fieldValue, $valueCharPos), '', $fieldValue): $fieldValue;
                    $nameCharPos = strpos($fieldFullName, '.');
                    $fieldName = $nameCharPos !== false ? substr($fieldFullName, $nameCharPos + 1): $fieldFullName;
                    $vars['dataFieldNames'][] = $fieldName;
                    $entityMetadata = $this->entityManager->getClassMetadata("App\\Entity\\{$classVars['entityFullName']}");
                    $dataFieldMap = array_combine($vars['dataFieldNames'], $vars['dataFieldNames']);
                    $dataFieldTypeMap = array_map(fn($fieldName) => $entityMetadata->getTypeOfField($fieldName), $dataFieldMap);
                    $vars['dataFieldSearchableMap'] = array_map(fn($fieldType) => $fieldType !== null && ($fieldType === 'string' || $fieldType === 'text'), $dataFieldTypeMap);
                }
            }
        }

        return $vars;
    }
}
