<?php

namespace App\Repository\Sale;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaleOrderHeaderRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleOrderHeader::class);
    }

    public function findRecentBy($year)
    {
        $dql = 'SELECT e FROM ' . SaleOrderHeader::class . ' e WHERE e.codeNumberYear = :codeNumberYear ORDER BY e.codeNumberOrdinal DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastSaleOrderHeader = $query->getOneOrNullResult();

        return $lastSaleOrderHeader;
    }
    
    public function findCustomerSaleOrderHeaders(array $customers, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . SaleOrderHeader::class . " e
                WHERE e.customer IN (:customers) AND e.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.customer ASC, e.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('customers', $customers);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $saleOrderHeaders = $query->getResult();

        return $saleOrderHeaders;
    }
    
    public function findProductSaleOrderHeaders(array $products, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . SaleOrderHeader::class . " e
                INNER JOIN " . SaleOrderDetail::class . " d ON e.id = d.saleOrderHeader
                WHERE d.product IN (:products) AND e.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY d.product ASC, e.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $saleOrderHeaders = $query->getResult();

        return $saleOrderHeaders;
    }
}
