<?php

namespace App\Common\Doctrine\Repository;

trait EntityRemove
{
    public function remove($entity, bool $flush = false): void
    {
        if (!str_contains(get_class($entity), $this->getEntityName())) {
            throw new \Exception('An invalid entity was passed as an argument');
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
