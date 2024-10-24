<?php

namespace App\Repository\Master;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Master\Paper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaperRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paper::class);
    }

    public function findRecentBy($materialSubCategory, $weight, $type)
    {
        $dql = 'SELECT e FROM ' . Paper::class . ' e WHERE e.materialSubCategory = :materialSubCategory AND e.weight = :weight AND e.type = :type ORDER BY e.code DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materialSubCategory', $materialSubCategory);
        $query->setParameter('weight', $weight);
        $query->setParameter('type', $type);
        $query->setMaxResults(1);
        $lastPaper = $query->getOneOrNullResult();

        return $lastPaper;
    }
}
