<?php

namespace App\Repository\Purchase;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseOrderPaperDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrderPaperDetail::class);
    }

    public function getAveragePriceList(array $papers): array
    {
        $dql = 'SELECT IDENTITY(e.paper) AS paperId, SUM(e.quantity * e.unitPrice) / SUM(e.quantity) AS averagePrice
                FROM ' . PurchaseOrderPaperDetail::class . ' e
                WHERE e.paper IN (:papers) AND e.isCanceled = false
                GROUP BY e.paper';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('papers', $papers);
        $averagePriceList = $query->getScalarResult();

        return $averagePriceList;
    }
    
    public function findPaperPurchaseOrderPaperDetails(array $papers, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . PurchaseOrderPaperDetail::class . " e
                INNER JOIN " . PurchaseOrderPaperHeader::class . " s
                WHERE e.paper IN (:papers) AND s.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.paper ASC, s.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('papers', $papers);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $purchaseOrderDetails = $query->getResult();

        return $purchaseOrderDetails;
    }
}
