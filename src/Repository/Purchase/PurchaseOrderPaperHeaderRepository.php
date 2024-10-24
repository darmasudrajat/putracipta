<?php

namespace App\Repository\Purchase;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseOrderPaperHeaderRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrderPaperHeader::class);
    }

    public function findRecentBy($year, $month)
    {
        $dql = 'SELECT e FROM ' . PurchaseOrderPaperHeader::class . ' e WHERE e.codeNumberMonth = :codeNumberMonth AND e.codeNumberYear = :codeNumberYear ORDER BY e.codeNumberOrdinal DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastPurchaseOrderPaperHeader = $query->getOneOrNullResult();

        return $lastPurchaseOrderPaperHeader;
    }
    
    public function findSupplierPurchaseOrderPaperHeaders(array $suppliers, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . PurchaseOrderPaperHeader::class . " e
                WHERE e.supplier IN (:suppliers) AND e.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.supplier ASC, e.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('suppliers', $suppliers);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $purchaseOrderPaperHeaders = $query->getResult();

        return $purchaseOrderPaperHeaders;
    }
}
