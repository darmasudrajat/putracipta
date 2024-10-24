<?php

namespace App\Repository\Master;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Master\Material;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MaterialRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Material::class);
    }

    public function findRecentBy($materialSubCategory)
    {
        $dql = 'SELECT e FROM ' . Material::class . ' e WHERE e.materialSubCategory = :materialSubCategory ORDER BY e.codeOrdinal DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materialSubCategory', $materialSubCategory);
        $query->setMaxResults(1);
        $lastMaterial = $query->getOneOrNullResult();

        return $lastMaterial;
    }
}
