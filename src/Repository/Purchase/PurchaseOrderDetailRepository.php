<?php

namespace App\Repository\Purchase;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseOrderDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrderDetail::class);
    }

    public function getAveragePriceList(array $materials): array
    {
        $dql = 'SELECT IDENTITY(e.material) AS materialId, SUM(e.quantity * e.unitPrice) / SUM(e.quantity) AS averagePrice
                FROM ' . PurchaseOrderDetail::class . ' e
                WHERE e.material IN (:materials) AND e.isCanceled = false
                GROUP BY e.material';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materials', $materials);
        $averagePriceList = $query->getScalarResult();

        return $averagePriceList;
    }
    
    public function findMaterialPurchaseOrderDetails(array $materials, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . PurchaseOrderDetail::class . " e
                INNER JOIN " . PurchaseOrderHeader::class . " s
                WHERE e.material IN (:materials) AND s.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.material ASC, s.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materials', $materials);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $purchaseOrderDetails = $query->getResult();

        return $purchaseOrderDetails;
    }
}
