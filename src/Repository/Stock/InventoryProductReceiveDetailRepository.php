<?php

namespace App\Repository\Stock;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Stock\InventoryProductReceiveDetail;
use App\Entity\Stock\InventoryProductReceiveHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InventoryProductReceiveDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryProductReceiveDetail::class);
    }
    
    public function findProductInventoryReceiveDetails(array $products, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . InventoryProductReceiveDetail::class . " e
                INNER JOIN " . InventoryProductReceiveHeader::class . " s
                WHERE e.product IN (:products) AND s.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.product ASC, s.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $inventoryProductReceiveDetails = $query->getResult();

        return $inventoryProductReceiveDetails;
    }
}
