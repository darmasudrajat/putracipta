<?php

namespace App\Repository\Sale;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaleOrderDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleOrderDetail::class);
    }

    public function getAveragePriceList(array $products): array
    {
        $dql = 'SELECT IDENTITY(e.product) AS productId, SUM(e.quantity * e.unitPrice) / SUM(e.quantity) AS averagePrice
                FROM ' . SaleOrderDetail::class . ' e
                WHERE e.product IN (:products) AND e.isCanceled = false
                GROUP BY e.product';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $averagePriceList = $query->getScalarResult();

        return $averagePriceList;
    }
    
    public function findProductSaleOrderDetails(array $products, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . SaleOrderDetail::class . " e
                INNER JOIN " . SaleOrderHeader::class . " s
                WHERE e.product IN (:products) AND s.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.product ASC, s.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $saleOrderDetails = $query->getResult();

        return $saleOrderDetails;
    }
}
