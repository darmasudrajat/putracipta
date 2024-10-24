<?php

namespace App\Command\Sync;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'custom:sync:scan',
    description: 'Scan for target entities'
)]
class SyncScanCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('entity-class', InputArgument::REQUIRED, 'The class name of the entity to scan');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->scan($input, $output);

        return Command::SUCCESS;
    }

    private function scan(InputInterface $input, OutputInterface $output): void
    {
        $this->scanForTargetEntities($output, $input->getArgument('entity-class'), 0);
    }

    private function scanForTargetEntities(OutputInterface $output, string $entityClass, int $relationLevel): void
    {
        $targetEntities = [];

        $reflectionClass = new \ReflectionClass($entityClass);
        $reflectionProperties = $reflectionClass->getProperties();
        foreach ($reflectionProperties as $reflectionProperty) {
            if ($reflectionProperty->getType()->getName() === Collection::class) {
                $reflectionAttributes = $reflectionProperty->getAttributes();
                foreach ($reflectionAttributes as $reflectionAttribute) {
                    if ($reflectionAttribute->getName() === OneToMany::class) {
                        $targetEntities[$reflectionProperty->getName()] = $reflectionAttribute->newInstance()->targetEntity;
                    }
                }
            }
        }

        if (empty($targetEntities)) {
            $output->write('null');
        } else {
            $output->writeln('[');
            foreach ($targetEntities as $propertyName => $targetEntity) {
                $output->write($this->makeSpaces($relationLevel + 1));
                $output->write("'{$propertyName}'");
                $output->write(' => ');
                $this->scanForTargetEntities($output, $targetEntity, $relationLevel + 1);
            }
            $output->write($this->makeSpaces($relationLevel));
            $output->write(']');
        }
        $output->writeln($relationLevel > 0 ? ',' : '');
    }

    private function makeSpaces(int $identationLevel): string
    {
        return str_repeat(' ', $identationLevel * 4);
    }
}
