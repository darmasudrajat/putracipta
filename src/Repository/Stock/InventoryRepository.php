<?php

namespace App\Repository\Stock;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Master\Warehouse;
use App\Entity\Stock\Inventory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InventoryRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventory::class);
    }

    public function getMaterialStockQuantityList(Warehouse $warehouse, array $materials): array
    {
        $dql = 'SELECT IDENTITY(e.material) AS materialId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.warehouse = :warehouse AND e.material IN (:materials) AND e.isReversed = false
                GROUP BY e.material';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('warehouse', $warehouse);
        $query->setParameter('materials', $materials);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getPaperStockQuantityList(Warehouse $warehouse, array $papers): array
    {
        $dql = 'SELECT IDENTITY(e.paper) AS paperId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.warehouse = :warehouse AND e.paper IN (:papers) AND e.isReversed = false
                GROUP BY e.paper';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('warehouse', $warehouse);
        $query->setParameter('papers', $papers);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getProductStockQuantityList(Warehouse $warehouse, array $products): array
    {
        $dql = 'SELECT IDENTITY(e.product) AS productId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.warehouse = :warehouse AND e.product IN (:products) AND e.isReversed = false
                GROUP BY e.product';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('warehouse', $warehouse);
        $query->setParameter('products', $products);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getAllWarehousePaperStockQuantityList($paper)
    {
        $dql = 'SELECT IDENTITY(e.paper) AS paperId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.paper = :paper AND e.isReversed = false
                GROUP BY e.paper';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('paper', $paper);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getAllWarehouseProductStockQuantityList(array $products): array
    {
        $dql = 'SELECT IDENTITY(e.product) AS productId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.product IN (:products) AND e.isReversed = false
                GROUP BY e.product';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getMaterialBeginningStockList(array $materials, $startDate, $warehouseId): array
    {
        $warehouseConditionString = !empty($warehouseId) ? 'AND IDENTITY(e.warehouse) = :warehouseId' : '';
        $dql = "SELECT IDENTITY(e.material) AS materialId, SUM(e.quantityIn - e.quantityOut) AS beginningStock
                FROM " . Inventory::class . " e
                WHERE e.material IN (:materials) AND e.isReversed = false AND e.transactionDate < :startDate {$warehouseConditionString}
                GROUP BY e.material";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materials', $materials);
        $query->setParameter('startDate', $startDate);
        if (!empty($warehouseId)) {
            $query->setParameter('warehouseId', $warehouseId);
        }
        $beginningStockList = $query->getScalarResult();

        return $beginningStockList;
    }

    public function getMaterialEndingStockList(array $materials, $endDate, $warehouseId): array
    {
        $warehouseConditionString = !empty($warehouseId) ? 'AND IDENTITY(e.warehouse) = :warehouseId' : '';
        $dql = "SELECT IDENTITY(e.material) AS materialId, SUM(e.quantityIn - e.quantityOut) AS endingStock
                FROM " . Inventory::class . " e
                WHERE e.material IN (:materials) AND e.isReversed = false AND e.transactionDate <= :endDate {$warehouseConditionString}
                GROUP BY e.material";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materials', $materials);
        $query->setParameter('endDate', $endDate);
        if (!empty($warehouseId)) {
            $query->setParameter('warehouseId', $warehouseId);
        }
        $endingStockList = $query->getScalarResult();

        return $endingStockList;
    }

    public function findMaterialInventories(array $materials, $startDate, $endDate, $warehouseId): array
    {
        $warehouseConditionString = !empty($warehouseId) ? 'AND IDENTITY(e.warehouse) = :warehouseId' : '';
        $dql = "SELECT e
                FROM " . Inventory::class . " e
                WHERE e.material IN (:materials) AND e.isReversed = false AND e.transactionDate BETWEEN :startDate AND :endDate {$warehouseConditionString}
                ORDER BY e.material ASC, e.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materials', $materials);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($warehouseId)) {
            $query->setParameter('warehouseId', $warehouseId);
        }
        $inventories = $query->getResult();

        return $inventories;
    }

    public function getPaperBeginningStockList(array $papers, $startDate, $warehouseId): array
    {
        $warehouseConditionString = !empty($warehouseId) ? 'AND IDENTITY(e.warehouse) = :warehouseId' : '';
        $dql = "SELECT IDENTITY(e.paper) AS paperId, SUM(e.quantityIn - e.quantityOut) AS beginningStock
                FROM " . Inventory::class . " e
                WHERE e.paper IN (:papers) AND e.isReversed = false AND e.transactionDate < :startDate {$warehouseConditionString}
                GROUP BY e.paper";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('papers', $papers);
        $query->setParameter('startDate', $startDate);
        if (!empty($warehouseId)) {
            $query->setParameter('warehouseId', $warehouseId);
        }
        $beginningStockList = $query->getScalarResult();

        return $beginningStockList;
    }

    public function getPaperEndingStockList(array $papers, $endDate, $warehouseId): array
    {
        $warehouseConditionString = !empty($warehouseId) ? 'AND IDENTITY(e.warehouse) = :warehouseId' : '';
        $dql = "SELECT IDENTITY(e.paper) AS paperId, SUM(e.quantityIn - e.quantityOut) AS endingStock
                FROM " . Inventory::class . " e
                WHERE e.paper IN (:papers) AND e.isReversed = false AND e.transactionDate <= :endDate {$warehouseConditionString}
                GROUP BY e.paper";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('papers', $papers);
        $query->setParameter('endDate', $endDate);
        if (!empty($warehouseId)) {
            $query->setParameter('warehouseId', $warehouseId);
        }
        $endingStockList = $query->getScalarResult();

        return $endingStockList;
    }

    public function findPaperInventories(array $papers, $startDate, $endDate): array
    {
        $dql = 'SELECT e
                FROM ' . Inventory::class . ' e
                WHERE e.paper IN (:papers) AND e.isReversed = false AND e.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.paper ASC, e.transactionDate ASC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('papers', $papers);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $inventories = $query->getResult();

        return $inventories;
    }

    public function getProductBeginningStockList(array $products, $startDate, $warehouseId): array
    {
        $warehouseConditionString = !empty($warehouseId) ? 'AND IDENTITY(e.warehouse) = :warehouseId' : '';
        $dql = "SELECT IDENTITY(e.product) AS productId, SUM(e.quantityIn - e.quantityOut) AS beginningStock
                FROM " . Inventory::class . " e
                WHERE e.product IN (:products) AND e.isReversed = false AND e.transactionDate < :startDate {$warehouseConditionString}
                GROUP BY e.product";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $query->setParameter('startDate', $startDate);
        if (!empty($warehouseId)) {
            $query->setParameter('warehouseId', $warehouseId);
        }
        $beginningStockList = $query->getScalarResult();

        return $beginningStockList;
    }

    public function getProductEndingStockList(array $products, $endDate, $warehouseId): array
    {
        $warehouseConditionString = !empty($warehouseId) ? 'AND IDENTITY(e.warehouse) = :warehouseId' : '';
        $dql = "SELECT IDENTITY(e.product) AS productId, SUM(e.quantityIn - e.quantityOut) AS endingStock
                FROM " . Inventory::class . " e
                WHERE e.product IN (:products) AND e.isReversed = false AND e.transactionDate <= :endDate {$warehouseConditionString}
                GROUP BY e.product";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $query->setParameter('endDate', $endDate);
        if (!empty($warehouseId)) {
            $query->setParameter('warehouseId', $warehouseId);
        }
        $endingStockList = $query->getScalarResult();

        return $endingStockList;
    }

    public function findProductInventories(array $products, $startDate, $endDate, $warehouseId): array
    {
        $warehouseConditionString = !empty($warehouseId) ? 'AND IDENTITY(e.warehouse) = :warehouseId' : '';
        $dql = "SELECT e
                FROM " . Inventory::class . " e
                WHERE e.product IN (:products) AND e.isReversed = false AND e.transactionDate BETWEEN :startDate AND :endDate {$warehouseConditionString}
                ORDER BY e.product ASC, e.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($warehouseId)) {
            $query->setParameter('warehouseId', $warehouseId);
        }
        $inventories = $query->getResult();

        return $inventories;
    }
}
