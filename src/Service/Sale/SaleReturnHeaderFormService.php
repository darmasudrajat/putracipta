<?php

namespace App\Service\Sale;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;
use App\Entity\Sale\SaleReturnDetail;
use App\Entity\Sale\SaleReturnHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Sale\SaleReturnDetailRepository;
use App\Repository\Sale\SaleReturnHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Sale\SaleReturnHeaderFormSupport;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SaleReturnHeaderFormService {

    use SaleReturnHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private SaleReturnHeaderRepository $saleReturnHeaderRepository;
    private SaleReturnDetailRepository $saleReturnDetailRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->saleReturnHeaderRepository = $entityManager->getRepository(SaleReturnHeader::class);
        $this->saleReturnDetailRepository = $entityManager->getRepository(SaleReturnDetail::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(SaleReturnHeader $saleReturnHeader, array $options = []): void {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($saleReturnHeader->getId())) {
            $saleReturnHeader->setCreatedTransactionDateTime($datetime);
            $saleReturnHeader->setCreatedTransactionUser($user);
        } else {
            $saleReturnHeader->setModifiedTransactionDateTime($datetime);
            $saleReturnHeader->setModifiedTransactionUser($user);
        }

        $saleReturnHeader->setCodeNumberVersion($saleReturnHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(SaleReturnHeader $saleReturnHeader, array $options = []): void {
        if ($saleReturnHeader->getTransactionDate() !== null && $saleReturnHeader->getId() === null) {
            $year = $saleReturnHeader->getTransactionDate()->format('y');
            $month = $saleReturnHeader->getTransactionDate()->format('m');
            $lastSaleReturnHeader = $this->saleReturnHeaderRepository->findRecentBy($year);
            $currentSaleReturnHeader = ($lastSaleReturnHeader === null) ? $saleReturnHeader : $lastSaleReturnHeader;
            $saleReturnHeader->setCodeNumberToNext($currentSaleReturnHeader->getCodeNumber(), $year, $month);
        }
        $deliveryHeader = $saleReturnHeader->getDeliveryHeader();
        if ($deliveryHeader !== null) {
            $deliveryHeader->setHasReturnTransaction(true);
            $saleReturnHeader->setCustomer($deliveryHeader->getCustomer());
        }

        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            $saleReturnDetail->setIsCanceled($saleReturnDetail->getSyncIsCanceled());
            $deliveryDetail = $saleReturnDetail->getDeliveryDetail();
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();

            $saleReturnDetail->setProduct($deliveryDetail->getProduct());
            $saleReturnDetail->setUnitPrice($saleOrderDetail->getUnitPriceBeforeTax());
            $saleReturnDetail->setUnit($deliveryDetail === null ? null : $deliveryDetail->getUnit());
            
            $saleReturnHeader->setTaxMode($saleOrderHeader->getTaxMode());
            $saleReturnHeader->setTaxPercentage($saleOrderHeader->getTaxPercentage());

            $saleOrderHeader->setHasReturnTransaction(true);
            
            $oldDeliveryDetails = [];
            $oldSaleReturnDetailsList = [];
            foreach ($oldDeliveryDetails as $oldDeliveryDetail) {
                $oldSaleReturnDetailsList[] = $this->saleReturnDetailRepository->findByDeliveryDetail($oldDeliveryDetail);
            }
            $totalReturn = 0;
            foreach ($oldSaleReturnDetailsList as $oldSaleReturnDetailsItem) {
                foreach ($oldSaleReturnDetailsItem as $oldSaleReturnDetail) {
                    if ($oldSaleReturnDetail->getId() !== $saleReturnDetail->getId()) {
                        $totalReturn += $oldSaleReturnDetail->getQuantity();
                    }
                }
            }

            if ($saleReturnHeader->isIsProductExchange() === true) {
                $totalReturn += $saleReturnDetail->getQuantity();
                $saleOrderDetail->setTotalQuantityReturn($totalReturn);
                $saleOrderDetail->setRemainingQuantityDelivery($saleOrderDetail->getSyncRemainingDelivery());
                if ($saleOrderDetail->getRemainingQuantityDelivery() > 0) {
                    $saleOrderDetail->setIsTransactionClosed(false);
                }

                $saleInvoiceDetails = $deliveryDetail->getSaleInvoiceDetails();
                if ($saleReturnHeader->getId() !== null && $saleInvoiceDetails !== null) {
                    foreach ($saleInvoiceDetails as $saleInvoiceDetail) {
                        $saleInvoiceDetail->setReturnAmount(0);
                        $saleInvoiceHeader = $saleInvoiceDetail->getSaleInvoiceHeader();
                        $saleInvoiceHeader->setTotalReturn(0);
                        $saleInvoiceHeader->setRemainingPayment($saleInvoiceHeader->getSyncRemainingPayment());
                    }
                }
                
                $totalRemainingDelivery = 0;
                foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
                    $totalRemainingDelivery += $saleOrderDetail->getRemainingQuantityDelivery();
                }
                $saleOrderHeader->setTotalRemainingDelivery($totalRemainingDelivery);
                $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_PARTIAL_DELIVERY);

            } else {
                $saleInvoiceDetails = $deliveryDetail->getSaleInvoiceDetails();
                if ($saleInvoiceDetails !== null) {
                    foreach ($saleInvoiceDetails as $saleInvoiceDetail) {
                        $saleInvoiceDetail->setReturnAmount($deliveryDetail->getSyncTotalReturn());
                        $saleInvoiceHeader = $saleInvoiceDetail->getSaleInvoiceHeader();
                        $saleInvoiceHeader->setTotalReturn($saleInvoiceHeader->getSyncTotalReturn());
                        $saleInvoiceHeader->setRemainingPayment($saleInvoiceHeader->getSyncRemainingPayment());
                    }
                }

                if ($saleReturnHeader->getId() !== null && $saleOrderDetail !== null) {
                    $saleOrderDetail->setTotalQuantityReturn($totalReturn);
                    $saleOrderDetail->setRemainingQuantityDelivery($saleOrderDetail->getSyncRemainingDelivery());
                }
            }
        }
        $saleReturnHeader->setSubTotal($saleReturnHeader->getSyncSubTotal());
        $saleReturnHeader->setTaxNominal($saleReturnHeader->getSyncTaxNominal());
        $saleReturnHeader->setGrandTotal($saleReturnHeader->getSyncGrandTotal());
        
        $saleOrderReferenceNumberList = [];
        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            $deliveryDetail = $saleReturnDetail->getDeliveryDetail();
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();
            $saleOrderReferenceNumberList[] = $saleOrderHeader->getReferenceNumber();
        }
        $saleOrderReferenceNumberUniqueList = array_unique(explode(', ', implode(', ', $saleOrderReferenceNumberList)));
        $saleReturnHeader->setSaleOrderReferenceNumbers(implode(', ', $saleOrderReferenceNumberUniqueList));
    }

    public function save(SaleReturnHeader $saleReturnHeader, array $options = []): void {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($saleReturnHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->saleReturnHeaderRepository->add($saleReturnHeader);
            foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
                $this->saleReturnDetailRepository->add($saleReturnDetail);
            }
            if ($saleReturnHeader->isIsProductExchange()) {
                $this->addInventories($saleReturnHeader);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($saleReturnHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }

    private function addInventories(SaleReturnHeader $saleReturnHeader): void {
        InventoryUtil::reverseOldData($this->inventoryRepository, $saleReturnHeader);
        $saleReturnDetails = $saleReturnHeader->getSaleReturnDetails()->toArray();
        $averagePriceList = InventoryUtil::getAveragePriceList('product', $this->saleOrderDetailRepository, $saleReturnDetails);
        InventoryUtil::addNewData($this->inventoryRepository, $saleReturnHeader, $saleReturnDetails, function ($newInventory, $saleReturnDetail) use ($averagePriceList, $saleReturnHeader) {
            $product = $saleReturnDetail->getProduct();
            $purchasePrice = isset($averagePriceList[$product->getId()]) ? $averagePriceList[$product->getId()] : '0.00';
            $newInventory->setTransactionSubject($saleReturnHeader->getCustomer()->getCompany());
            $newInventory->setPurchasePrice($purchasePrice);
            $newInventory->setProduct($product);
            $newInventory->setWarehouse($saleReturnHeader->getWarehouse());
            $newInventory->setInventoryMode('product');
            $newInventory->setQuantityIn($saleReturnDetail->getQuantity());
        });
    }
}
