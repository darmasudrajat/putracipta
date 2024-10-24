<?php

namespace App\Common\Sync;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\OneToMany;

trait EntitySyncScan
{
    private array $associations = [];
    private array $relations = [];
    private array $managedEntities = [];
    private array $persistedEntities = [];
    private bool $oldEntitiesScanned = false;
    private bool $newEntitiesScanned = false;

    private function getRelationsFrom(string $entityClass): ?array
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
            return null;
        } else {
            $relations = [];
            foreach ($targetEntities as $propertyName => $targetEntity) {
                $subRelations = $this->getRelationsFrom($targetEntity);
                $relations[$propertyName] = $subRelations;
            }
            return $relations;
        }
    }

    public function scanForOldEntities($rootEntity): void
    {
        if (!$this->oldEntitiesScanned && !$this->newEntitiesScanned) {
            $this->putEntityToScanned($rootEntity, '_', false);
            $this->scanForEntities($rootEntity, $this->relations, false);
            $this->oldEntitiesScanned = true;
        }
    }

    public function scanForNewEntities($rootEntity): void
    {
        if ($this->oldEntitiesScanned && !$this->newEntitiesScanned) {
            $this->putEntityToScanned($rootEntity, '_', true);
            $this->scanForEntities($rootEntity, $this->relations, true);
            $this->newEntitiesScanned = true;
        }
    }

    public function update(EntityManagerInterface $entityManager): void
    {
        if ($this->oldEntitiesScanned && $this->newEntitiesScanned) {
            foreach ($this->persistedEntities as $persistedEntity) {
                $entityManager->persist($persistedEntity);
            }
            foreach ($this->managedEntities as $managedEntityItem) {
                if ($managedEntityItem[1] > $managedEntityItem[2]) {
                    $entityManager->remove($managedEntityItem[0]);
                }
            }
        }
    }

    public function doWhileScanning($rootEntity, callable $actionBefore, callable $actionAfter): void
    {
        $actionBefore($rootEntity);
        $this->doWhileScanningFor($rootEntity, $this->associations[get_class($rootEntity)], $actionBefore, $actionAfter);
        $actionAfter($rootEntity);
    }

    public function doBeforeWhileScanning($rootEntity, callable $action): void
    {
        $action($rootEntity);
        $this->doWhileScanningFor($rootEntity, $this->associations[get_class($rootEntity)], $action, null);
    }

    public function doAfterWhileScanning($rootEntity, callable $action): void
    {
        $this->doWhileScanningFor($rootEntity, $this->associations[get_class($rootEntity)], null, $action);
        $action($rootEntity);
    }

    public function getView(): array
    {
        $countByRelation = [];
        foreach (array_keys($this->managedEntities) as $key) {
            list($relationName, ) = explode('|', $key);
            if (!isset($countByRelation[$relationName])) {
                $countByRelation[$relationName] = 0;
            }
            $countByRelation[$relationName]++;
        }
        return [
            'countByRelation' => $countByRelation,
        ];
    }

    private function setupAssociations(string $entityClass): void
    {
        $this->associations[$entityClass] = $this->getRelationsFrom($entityClass);
    }

    private function setupRelations(array $relations): void
    {
        $this->relations = $relations;
    }

    private function doWhileScanningFor($parentEntity, ?array $relations, ?callable $actionBefore, ?callable $actionAfter): void
    {
        if ($relations !== null) {
            foreach ($relations as $relationName => $subRelations) {
                $getterName = 'get' . ucfirst($relationName);
                $objectOrCollection = $parentEntity->$getterName();
                $entities = $objectOrCollection instanceof Collection ? $objectOrCollection->getValues() : [$objectOrCollection];
                foreach ($entities as $entity) {
                    if ($actionBefore !== null) {
                        $actionBefore($entity);
                    }
                    if (is_array($subRelations)) {
                        $this->doWhileScanningFor($entity, $subRelations, $actionBefore, $actionAfter);
                    }
                    if ($actionAfter !== null) {
                        $actionAfter($entity);
                    }
                }
            }
        }
    }

    private function scanForEntities($parentEntity, ?array $relations, bool $isNew): void
    {
        if ($relations !== null) {
            foreach ($relations as $relationName => $subRelations) {
                $getterName = 'get' . ucfirst($relationName);
                $objectOrCollection = $parentEntity->$getterName();
                $entities = $objectOrCollection instanceof Collection ? $objectOrCollection->getValues() : [$objectOrCollection];
                foreach ($entities as $entity) {
                    $this->putEntityToScanned($entity, $relationName, $isNew);
                    if (is_array($subRelations)) {
                        $this->scanForEntities($entity, $subRelations, $isNew);
                    }
                }
            }
        }
    }

    private function putEntityToScanned($entity, string $relationName, bool $isNew): void
    {
        $entityId = $entity->getId();
        if ($entityId === null) {
            if ($isNew) {
                $this->persistedEntities[] = $entity;
            }
        } else {
            $key = $relationName . '|' . $entityId;
            if (!isset($this->managedEntities[$key])) {
                $this->managedEntities[$key] = [$entity, 0, 0];
            }
            if ($isNew) {
                $this->managedEntities[$key][2]++;
            } else {
                $this->managedEntities[$key][1]++;
            }
        }
    }
}
