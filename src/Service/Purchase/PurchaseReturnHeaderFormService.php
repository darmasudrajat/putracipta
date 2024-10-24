<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseReturnDetail;
use App\Entity\Purchase\PurchaseReturnHeader;
use App\Entity\Purchase\ReceiveDetail;
use App\Entity\Purchase\ReceiveHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Purchase\PurchaseReturnDetailRepository;
use App\Repository\Purchase\PurchaseReturnHeaderRepository;
use App\Repository\Purchase\ReceiveDetailRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Purchase\PurchaseReturnHeaderFormSupport;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseReturnHeaderFormService
{
    use PurchaseReturnHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;
    private PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository;
    private PurchaseReturnDetailRepository $purchaseReturnDetailRepository;
    private ReceiveDetailRepository $receiveDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
        $this->purchaseReturnHeaderRepository = $entityManager->getRepository(PurchaseReturnHeader::class);
        $this->purchaseReturnDetailRepository = $entityManager->getRepository(PurchaseReturnDetail::class);
        $this->receiveDetailRepository = $entityManager->getRepository(ReceiveDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($purchaseReturnHeader->getId())) {
            $purchaseReturnHeader->setCreatedTransactionDateTime($datetime);
            $purchaseReturnHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseReturnHeader->setModifiedTransactionDateTime($datetime);
            $purchaseReturnHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        if ($purchaseReturnHeader->getTransactionDate() !== null && $purchaseReturnHeader->getId() === null) {
            $year = $purchaseReturnHeader->getTransactionDate()->format('y');
            $month = $purchaseReturnHeader->getTransactionDate()->format('m');
            $lastPurchaseReturnHeader = $this->purchaseReturnHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseReturnHeader = ($lastPurchaseReturnHeader === null) ? $purchaseReturnHeader : $lastPurchaseReturnHeader;
            $purchaseReturnHeader->setCodeNumberToNext($currentPurchaseReturnHeader->getCodeNumber(), $year, $month);

        }
        $receiveHeader = $purchaseReturnHeader->getReceiveHeader();
        $purchaseReturnHeader->setSupplier($receiveHeader === null ? null : $receiveHeader->getSupplier());
        
        if ($receiveHeader !== null) {
            $receiveHeader->setHasReturnTransaction(true);
            $purchaseOrderHeaderForMaterialOrPaper = $this->getPurchaseOrderHeaderForMaterialOrPaper($receiveHeader);
            
            if ($purchaseOrderHeaderForMaterialOrPaper !== null) {
                $purchaseOrderHeaderForMaterialOrPaper->setHasReturnTransaction(true);
            }
        }
        
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $purchaseReturnDetail->setIsCanceled($purchaseReturnDetail->getSyncIsCanceled());
            $receiveDetail = $purchaseReturnDetail->getReceiveDetail();
            $purchaseOrderDetail = empty($receiveDetail->getPurchaseOrderDetail()) ? $receiveDetail->getPurchaseOrderPaperDetail(): $receiveDetail->getPurchaseOrderDetail();
            $purchaseReturnDetail->setMaterial($receiveDetail->getMaterial());
            $purchaseReturnDetail->setPaper($receiveDetail->getPaper());
            $purchaseReturnDetail->setUnitPrice($purchaseOrderDetail->getUnitPriceBeforeTax());
            $purchaseReturnDetail->setUnit($receiveDetail === null ? null : $receiveDetail->getUnit());
            
        }
        
        $purchaseReturnHeader->setSubTotal($purchaseReturnHeader->getSyncSubTotal());
        if ($purchaseReturnHeader->getTaxMode() !== $purchaseReturnHeader::TAX_MODE_NON_TAX) {
            $purchaseReturnHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $purchaseReturnHeader->setTaxPercentage(0);
        }
        $purchaseReturnHeader->setTaxNominal($purchaseReturnHeader->getSyncTaxNominal());
        $purchaseReturnHeader->setGrandTotal($purchaseReturnHeader->getSyncGrandTotal());
        
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $receiveDetail = $purchaseReturnDetail->getReceiveDetail();
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $purchaseInvoiceHeaders = $receiveHeader === null ? null : $receiveHeader->getPurchaseInvoiceHeaders();
            
            $oldReceiveDetails = [];
            if ($purchaseOrderDetailForMaterialOrPaper instanceof PurchaseOrderDetail) {
                $oldReceiveDetails = $this->receiveDetailRepository->findByPurchaseOrderDetail($purchaseOrderDetailForMaterialOrPaper);
            } else if ($purchaseOrderDetailForMaterialOrPaper instanceof PurchaseOrderPaperDetail) {
                $oldReceiveDetails = $this->receiveDetailRepository->findByPurchaseOrderPaperDetail($purchaseOrderDetailForMaterialOrPaper);
            }
            $oldPurchaseReturnDetailsList = [];
            foreach ($oldReceiveDetails as $oldReceiveDetail) {
                $oldPurchaseReturnDetailsList[] = $this->purchaseReturnDetailRepository->findByReceiveDetail($oldReceiveDetail);
            }
            $totalReturn = 0;
            foreach ($oldPurchaseReturnDetailsList as $oldPurchaseReturnDetailsItem) {
                foreach ($oldPurchaseReturnDetailsItem as $oldPurchaseReturnDetail) {
                    if ($oldPurchaseReturnDetail->getId() !== $purchaseReturnDetail->getId()) {
                        $totalReturn += $oldPurchaseReturnDetail->getQuantity();
                    }
                }
            }
            
            if ($purchaseReturnHeader->isIsProductExchange() === true) {
                $totalReturn += $purchaseReturnDetail->getQuantity();
                $purchaseOrderDetailForMaterialOrPaper->setTotalReturn($totalReturn);
                $purchaseOrderDetailForMaterialOrPaper->setRemainingReceive($purchaseOrderDetailForMaterialOrPaper->getSyncRemainingReceive());
                if ($purchaseOrderDetailForMaterialOrPaper->getRemainingReceive() > 0) {
                    $purchaseOrderDetailForMaterialOrPaper->setIsTransactionClosed(false);
                }
                
                if ($purchaseReturnHeader->getId() !== null && $purchaseInvoiceHeaders !== null) {
                    foreach ($purchaseInvoiceHeaders as $purchaseInvoiceHeader) {
                        $purchaseInvoiceHeader->setTotalReturn(0);
                        $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
                    }
                }
            } else {
                if ($purchaseInvoiceHeaders !== null) {
                    foreach ($purchaseInvoiceHeaders as $purchaseInvoiceHeader) {
                        $purchaseInvoiceHeader->setTotalReturn($purchaseReturnHeader->getGrandTotal());
                        $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
                    }
                }
                
                if ($purchaseReturnHeader->getId() !== null && $purchaseOrderDetailForMaterialOrPaper !== null) {
                    $purchaseOrderDetailForMaterialOrPaper->setTotalReturn($totalReturn);
                    $purchaseOrderDetailForMaterialOrPaper->setRemainingReceive($purchaseOrderDetailForMaterialOrPaper->getSyncRemainingReceive());
                }
            }
        }
    }

    public function save(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($purchaseReturnHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->purchaseReturnHeaderRepository->add($purchaseReturnHeader);
            foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
                $this->purchaseReturnDetailRepository->add($purchaseReturnDetail);
            }
            $this->addInventories($purchaseReturnHeader);
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($purchaseReturnHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }

    private function getPurchaseOrderHeaderForMaterialOrPaper(ReceiveHeader $receiveHeader)
    {
        $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
        $purchaseOrderPaperHeader = $receiveHeader->getPurchaseOrderPaperHeader();
        if ($purchaseOrderHeader === null && $purchaseOrderPaperHeader === null) {
            return null;
        } else if ($purchaseOrderPaperHeader === null && $purchaseOrderHeader !== null) {
            return $purchaseOrderHeader;
        } else if ($purchaseOrderHeader === null && $purchaseOrderPaperHeader !== null) {
            return $purchaseOrderPaperHeader;
        }
    }

    private function getPurchaseOrderDetailForMaterialOrPaper(ReceiveDetail $receiveDetail)
    {
        $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
        $purchaseOrderPaperDetail = $receiveDetail->getPurchaseOrderPaperDetail();
        if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail === null) {
            return null;
        } else if ($purchaseOrderPaperDetail === null && $purchaseOrderDetail !== null) {
            return $purchaseOrderDetail;
        } else if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail !== null) {
            return $purchaseOrderPaperDetail;
        }
    }

    private function addInventories(PurchaseReturnHeader $purchaseReturnHeader): void
    {
        InventoryUtil::reverseOldData($this->inventoryRepository, $purchaseReturnHeader);
        
        $purchaseReturnDetails = $purchaseReturnHeader->getPurchaseReturnDetails()->toArray();
        if (!empty($purchaseReturnDetails[0]->getMaterial())) {
            $averagePriceList = InventoryUtil::getAveragePriceList('material', $this->purchaseOrderDetailRepository, $purchaseReturnDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $purchaseReturnHeader, $purchaseReturnDetails, function($newInventory, $purchaseReturnDetail) use ($averagePriceList, $purchaseReturnHeader) {
                $material = $purchaseReturnDetail->getMaterial();
                $purchasePrice = isset($averagePriceList[$material->getId()]) ? $averagePriceList[$material->getId()] : '0.00';
                $newInventory->setTransactionSubject($purchaseReturnHeader->getSupplier()->getCompany());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setMaterial($material);
                $newInventory->setWarehouse($purchaseReturnHeader->getWarehouse());
                $newInventory->setInventoryMode('material');
                $newInventory->setQuantityOut($purchaseReturnDetail->getQuantity());
            });
        } else if (!empty($purchaseReturnDetails[0]->getPaper())) {
            $averagePriceList = InventoryUtil::getAveragePriceList('paper', $this->purchaseOrderPaperDetailRepository, $purchaseReturnDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $purchaseReturnHeader, $purchaseReturnDetails, function($newInventory, $purchaseReturnDetail) use ($averagePriceList, $purchaseReturnHeader) {
                $paper = $purchaseReturnDetail->getPaper();
                $purchasePrice = isset($averagePriceList[$paper->getId()]) ? $averagePriceList[$paper->getId()] : '0.00';
                $newInventory->setTransactionSubject($purchaseReturnHeader->getSupplier()->getCompany());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setPaper($paper);
                $newInventory->setWarehouse($purchaseReturnHeader->getWarehouse());
                $newInventory->setInventoryMode('paper');
                $newInventory->setQuantityOut($purchaseReturnDetail->getQuantity());
            });
        }
    }
}
