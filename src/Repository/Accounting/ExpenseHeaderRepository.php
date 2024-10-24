<?php

namespace App\Repository\Accounting;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Accounting\ExpenseHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExpenseHeaderRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseHeader::class);
    }

    public function findRecentBy($year, $month)
    {
        $dql = 'SELECT e FROM ' . ExpenseHeader::class . ' e WHERE e.codeNumberMonth = :codeNumberMonth AND e.codeNumberYear = :codeNumberYear ORDER BY e.codeNumberOrdinal DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastExpenseHeader = $query->getOneOrNullResult();

        return $lastExpenseHeader;
    }
}
