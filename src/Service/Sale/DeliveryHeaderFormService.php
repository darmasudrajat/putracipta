<?php

namespace App\Service\Sale;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleOrderHeader;
use App\Entity\Sale\DeliveryDetail;
use App\Entity\Sale\DeliveryHeader;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Sale\DeliveryDetailRepository;
use App\Repository\Sale\DeliveryHeaderRepository;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Sale\DeliveryHeaderFormSupport;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DeliveryHeaderFormService
{
    use DeliveryHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private DeliveryHeaderRepository $deliveryHeaderRepository;
    private DeliveryDetailRepository $deliveryDetailRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->deliveryHeaderRepository = $entityManager->getRepository(DeliveryHeader::class);
        $this->deliveryDetailRepository = $entityManager->getRepository(DeliveryDetail::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(DeliveryHeader $deliveryHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($deliveryHeader->getId())) {
            $deliveryHeader->setCreatedTransactionDateTime($datetime);
            $deliveryHeader->setCreatedTransactionUser($user);
        } else {
            $deliveryHeader->setModifiedTransactionDateTime($datetime);
            $deliveryHeader->setModifiedTransactionUser($user);
        }
        
        $deliveryHeader->setCodeNumberVersion($deliveryHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(DeliveryHeader $deliveryHeader, array $options = []): void
    {
        if ($deliveryHeader->getTransactionDate() !== null && $deliveryHeader->getId() === null) {
            $year = $deliveryHeader->getTransactionDate()->format('y');
            $month = $deliveryHeader->getTransactionDate()->format('m');
            $lastDeliveryHeader = $this->deliveryHeaderRepository->findRecentBy($year);
            $currentDeliveryHeader = ($lastDeliveryHeader === null) ? $deliveryHeader : $lastDeliveryHeader;
            $deliveryHeader->setCodeNumberToNext($currentDeliveryHeader->getCodeNumber(), $year, $month);
        }
        $transportation = $deliveryHeader->getTransportation();
        if ($deliveryHeader->isIsUsingOutsourceDelivery() === false && $transportation !== null) {
            $deliveryHeader->setVehicleName($transportation->getName());
            $deliveryHeader->setVehiclePlateNumber($transportation->getPlateNumber());
//            $deliveryHeader->setVehicleDriverName($deliveryHeader->getEmployee()->getName());
        }
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $deliveryDetail->setProduct($saleOrderDetail->getProduct());
            $deliveryDetail->setUnit($saleOrderDetail->getUnit());
            $deliveryDetail->setLinePo($saleOrderDetail->getLinePo());
            $deliveryDetail->setLotNumber($deliveryDetail->getMasterOrderOrdinalYear());
            $deliveryHeader->setDeliveryAddressOrdinal($saleOrderDetail->getSaleOrderHeader()->getDeliveryAddressOrdinal());
        }
        $deliveryHeader->setTotalQuantity($deliveryHeader->getSyncTotalQuantity());
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $masterOrderProductDetail = $deliveryDetail->getMasterOrderProductDetail();
            $oldDeliveryDetails = $this->deliveryDetailRepository->findBySaleOrderDetail($saleOrderDetail);
            $oldMasterOrderProductDetails = $this->deliveryDetailRepository->findByMasterOrderProductDetail($masterOrderProductDetail);
            $totalDelivery = 0;
            foreach ($oldDeliveryDetails as $oldDeliveryDetail) {
                if ($oldDeliveryDetail->getId() !== $deliveryDetail->getId() && $oldDeliveryDetail->isIsCanceled() === false) {
                    $totalDelivery += $oldDeliveryDetail->getQuantity();
                }
            }
            if ($deliveryDetail->isIsCanceled() === false) {
                $totalDelivery += $deliveryDetail->getQuantity();
            }
            $saleOrderDetail->setTotalQuantityDelivery($totalDelivery);
            $saleOrderDetail->setRemainingQuantityDelivery($saleOrderDetail->getSyncRemainingDelivery());
            
            if ($deliveryHeader->getId() === null) {
                $deliveryDetail->setDeliveredQuantity($saleOrderDetail->getTotalQuantityDelivery());
                $deliveryDetail->setRemainingQuantity($saleOrderDetail->getRemainingQuantityDelivery());
            }
                
            $totalDeliveryMasterOrder = 0;
            foreach ($oldMasterOrderProductDetails as $oldMasterOrderProductDetail) {
                if ($oldMasterOrderProductDetail->getId() !== $deliveryDetail->getId() && $oldMasterOrderProductDetail->isIsCanceled() === false) {
                    $totalDeliveryMasterOrder += $oldMasterOrderProductDetail->getQuantity();
                }
            }
            if ($deliveryDetail->isIsCanceled() === false) {
                $totalDeliveryMasterOrder += $deliveryDetail->getQuantity();
            }
            $masterOrderProductDetail->setQuantityDelivery($totalDeliveryMasterOrder);
            $masterOrderProductDetail->setRemainingStockDelivery($masterOrderProductDetail->getSyncRemainingStockDelivery());
            
        }
        
        if ($deliveryHeader->getId() === null) {
            $products = array_map(fn($deliveryDetail) => $deliveryDetail->getProduct(), $deliveryHeader->getDeliveryDetails()->toArray());
            $stockQuantityList = $this->inventoryRepository->getAllWarehouseProductStockQuantityList($products);
            $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'productId');
            foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
                $product = $deliveryDetail->getProduct();
                $stockQuantity = isset($stockQuantityListIndexed[$product->getId()]) ? $stockQuantityListIndexed[$product->getId()] : 0;
                $deliveryDetail->setQuantityCurrent($stockQuantity);
            }
        }

        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();
            $deliveryHeader->setIsUsingFscPaper($saleOrderHeader->isIsUsingFscPaper());
            
            $totalRemainingDelivery = 0;
            foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
                $totalRemainingDelivery += $saleOrderDetail->getRemainingQuantityDelivery();
            }
            $saleOrderHeader->setTotalRemainingDelivery($totalRemainingDelivery);
            
            if ($totalRemainingDelivery > 0) {
                $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_PARTIAL_DELIVERY);
            } else {
                $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_FULL_DELIVERY);
            }
        }
        
        $saleOrderReferenceNumberList = [];
        $deliveryDetailProductCodeList = [];
        $deliveryDetailProductList = [];
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();
            $saleOrderReferenceNumberList[] = $saleOrderHeader->getReferenceNumber();
            $deliveryDetailProductCodeList[] = $saleOrderDetail->getProduct()->getCode();
            $deliveryDetailProductList[] = $saleOrderDetail->getProduct()->getName();
        }
        $saleOrderReferenceNumberUniqueList = array_unique(explode(', ', implode(', ', $saleOrderReferenceNumberList)));
        $deliveryHeader->setSaleOrderReferenceNumbers(implode(', ', $saleOrderReferenceNumberUniqueList));
        $deliveryDetailProductCodeUniqueList = array_unique(explode(', ', implode(', ', $deliveryDetailProductCodeList)));
        $deliveryHeader->setDeliveryDetailProductCodeList(implode(', ', $deliveryDetailProductCodeUniqueList));
        $deliveryDetailProductUniqueList = array_unique(explode(', ', implode(', ', $deliveryDetailProductList)));
        $deliveryHeader->setDeliveryDetailProductList(implode(', ', $deliveryDetailProductUniqueList));
    }

    public function save(DeliveryHeader $deliveryHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($deliveryHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->deliveryHeaderRepository->add($deliveryHeader);
            foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
                $this->deliveryDetailRepository->add($deliveryDetail);
            }
            $this->addInventories($deliveryHeader);
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($deliveryHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }

    private function addInventories(DeliveryHeader $deliveryHeader): void
    {
        InventoryUtil::reverseOldData($this->inventoryRepository, $deliveryHeader);
        $deliveryDetails = $deliveryHeader->getDeliveryDetails()->toArray();
        $averagePriceList = InventoryUtil::getAveragePriceList('product', $this->saleOrderDetailRepository, $deliveryDetails);
        InventoryUtil::addNewData($this->inventoryRepository, $deliveryHeader, $deliveryDetails, function($newInventory, $deliveryDetail) use ($averagePriceList, $deliveryHeader) {
            $product = $deliveryDetail->getProduct();
            $purchasePrice = isset($averagePriceList[$product->getId()]) ? $averagePriceList[$product->getId()] : '0.00';
            $newInventory->setTransactionSubject($deliveryHeader->getCustomer()->getCompany());
            $newInventory->setPurchasePrice($purchasePrice);
            $newInventory->setProduct($product);
            $newInventory->setWarehouse($deliveryHeader->getWarehouse());
            $newInventory->setInventoryMode('product');
            $newInventory->setQuantityOut($deliveryDetail->getQuantity());
        });
    }
}
