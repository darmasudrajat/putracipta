<?php

namespace App\Common\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;
    private string $className;

    public function __construct(EntityManagerInterface $entityManager, string $className)
    {
        $this->entityManager = $entityManager;
        $this->className = $className;
    }

    public function transform($entity = null)
    {
        if (null === $entity) {
            return '';
        }

        if (!($entity instanceof $this->className)) {
            throw new TransformationFailedException(sprintf(
                'An entity is not an instance of "%s"!', $this->className
            ));
        }

        return $entity->getId();
    }

    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->entityManager->find($this->className, $id);

        if (null === $entity) {
            throw new TransformationFailedException(sprintf(
                'An entity "%s" with id "%d" does not exist!', $this->className, $id
            ));
        }

        return $entity;
    }
}
