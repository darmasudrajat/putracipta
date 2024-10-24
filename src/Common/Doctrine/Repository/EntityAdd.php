<?php

namespace App\Common\Doctrine\Repository;

trait EntityAdd
{
    public function add($entity, bool $flush = false): void
    {
        if (!str_contains(get_class($entity), $this->getEntityName())) {
            throw new \Exception('An invalid entity was passed as an argument');
        }

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
