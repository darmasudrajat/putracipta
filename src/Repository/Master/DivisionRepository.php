<?php

namespace App\Repository\Master;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Master\Division;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DivisionRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Division::class);
    }
    
    public function findTransportationRecord()
    {
        return $this->find(3);
    }
    
    public function findMarketingRecord()
    {
        return $this->find(2);
    }
    
    public function findDevelopmentRecord()
    {
        return $this->find(4);
    }
}
