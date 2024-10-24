<?php

namespace App\Repository\Admin;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Admin\LiteralConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LiteralConfigRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiteralConfig::class);
    }

    public function findLiteralValue(string $literalName) 
    {
        $dql = "SELECT e.{$literalName} FROM " . LiteralConfig::class . ' e';

        $query = $this->getEntityManager()->createQuery($dql);
        try {
            $literalValue = $query->getSingleScalarResult();
            return $literalValue === null ? null : $literalValue;
        } catch (\Exception $e) {
            return null;
        }

        return $literalValue;
    }
}
