<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Entity\Purchase\PurchaseRequestDetail;
use App\Entity\Purchase\ReceiveDetail;
use App\Entity\Purchase\ReceiveHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderHeaderRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperHeaderRepository;
use App\Repository\Purchase\ReceiveDetailRepository;
use App\Repository\Purchase\ReceiveHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Purchase\ReceiveHeaderFormSupport;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ReceiveHeaderFormService
{
    use ReceiveHeaderFormSupport;
    
    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private ReceiveHeaderRepository $receiveHeaderRepository;
    private ReceiveDetailRepository $receiveDetailRepository;
    private PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;
    private PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->receiveHeaderRepository = $entityManager->getRepository(ReceiveHeader::class);
        $this->receiveDetailRepository = $entityManager->getRepository(ReceiveDetail::class);
        $this->purchaseOrderHeaderRepository = $entityManager->getRepository(PurchaseOrderHeader::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
        $this->purchaseOrderPaperHeaderRepository = $entityManager->getRepository(PurchaseOrderPaperHeader::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(ReceiveHeader $receiveHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($receiveHeader->getId())) {
            $receiveHeader->setCreatedTransactionDateTime($datetime);
            $receiveHeader->setCreatedTransactionUser($user);
        } else {
            $receiveHeader->setModifiedTransactionDateTime($datetime);
            $receiveHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(ReceiveHeader $receiveHeader, array $options = []): void
    {
        if ($receiveHeader->getTransactionDate() !== null && $receiveHeader->getId() === null) {
            $year = $receiveHeader->getTransactionDate()->format('y');
            $month = $receiveHeader->getTransactionDate()->format('m');
            $lastReceiveHeader = $this->receiveHeaderRepository->findRecentBy($year, $month);
            $currentReceiveHeader = ($lastReceiveHeader === null) ? $receiveHeader : $lastReceiveHeader;
            $receiveHeader->setCodeNumberToNext($currentReceiveHeader->getCodeNumber(), $year, $month);

        }
        $purchaseOrderHeaderForMaterialOrPaper = $this->getPurchaseOrderHeaderForMaterialOrPaper($receiveHeader);
        if ($purchaseOrderHeaderForMaterialOrPaper !== null) {
            $receiveHeader->setPurchaseOrderCodeNumberOrdinal($purchaseOrderHeaderForMaterialOrPaper->getCodeNumberOrdinal());
            $receiveHeader->setPurchaseOrderCodeNumberMonth($purchaseOrderHeaderForMaterialOrPaper->getCodeNumberMonth());
            $receiveHeader->setPurchaseOrderCodeNumberYear($purchaseOrderHeaderForMaterialOrPaper->getCodeNumberYear());
        }
        $receiveHeader->setSupplier($purchaseOrderHeaderForMaterialOrPaper === null ? null : $purchaseOrderHeaderForMaterialOrPaper->getSupplier());
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $this->setMaterialOrPaper($receiveDetail, $purchaseOrderDetailForMaterialOrPaper);
        }
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $receiveDetail->setIsCanceled($receiveDetail->getSyncIsCanceled());
            $receiveDetail->setUnit($purchaseOrderDetailForMaterialOrPaper === null ? null : $purchaseOrderDetailForMaterialOrPaper->getUnit());
        }
        $receiveHeader->setTotalQuantity($receiveHeader->getSyncTotalQuantity());
        
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $oldReceiveDetails = empty($receiveDetail->getPurchaseOrderDetail()) ? $this->receiveDetailRepository->findByPurchaseOrderPaperDetail($purchaseOrderDetailForMaterialOrPaper) : $this->receiveDetailRepository->findByPurchaseOrderDetail($purchaseOrderDetailForMaterialOrPaper);
            $totalReceive = 0;
            foreach ($oldReceiveDetails as $oldReceiveDetail) {
                if ($oldReceiveDetail->getId() !== $receiveDetail->getId() && $oldReceiveDetail->isIsCanceled() === false) {
                    $totalReceive += $oldReceiveDetail->getReceivedQuantity();
                }
            }
            $totalReceive += $receiveDetail->isIsCanceled() === true ? '0.00' : $receiveDetail->getReceivedQuantity();
            $purchaseOrderDetailForMaterialOrPaper->setTotalReceive($totalReceive);
            $purchaseOrderDetailForMaterialOrPaper->setRemainingReceive($purchaseOrderDetailForMaterialOrPaper->getSyncRemainingReceive());
            $receiveDetail->setRemainingQuantity($purchaseOrderDetailForMaterialOrPaper->getRemainingReceive());
        }
        
        $totalRemaining = 0;
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $totalRemaining += $purchaseOrderDetailForMaterialOrPaper->getRemainingReceive();
        
            $purchaseRequestDetailForMaterialOrPaper = $this->getPurchaseRequestDetailForMaterialOrPaper($receiveDetail);
            if ($purchaseRequestDetailForMaterialOrPaper !== null) {
                if ($totalRemaining > 0) {
                    $purchaseRequestDetailForMaterialOrPaper->setTransactionStatus(PurchaseRequestDetail::TRANSACTION_STATUS_RECEIVE);
                } else {
                    $purchaseRequestDetailForMaterialOrPaper->setTransactionStatus(PurchaseRequestDetail::TRANSACTION_STATUS_CLOSE);
                }
            }
        }
        
        if ($purchaseOrderHeaderForMaterialOrPaper !== null) {
            if ($totalRemaining > 0) {
                $purchaseOrderHeaderForMaterialOrPaper->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_PARTIAL_RECEIVE);
            } else {
                $purchaseOrderHeaderForMaterialOrPaper->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_FULL_RECEIVE);
            }
            $purchaseOrderHeaderForMaterialOrPaper->setTotalRemainingReceive($purchaseOrderHeaderForMaterialOrPaper->getSyncTotalRemainingReceive());
        }
    }

    public function save(ReceiveHeader $receiveHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($receiveHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
            $purchaseOrderPaperHeader = $receiveHeader->getPurchaseOrderPaperHeader();
            $this->receiveHeaderRepository->add($receiveHeader);

            if ($purchaseOrderHeader !== null) {
                $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader);
            } else {
                $this->purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader);
            }

            foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
                $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
                $purchaseOrderPaperDetail = $receiveDetail->getPurchaseOrderPaperDetail();
                $this->receiveDetailRepository->add($receiveDetail);

                if ($purchaseOrderHeader !== null) {
                    $this->purchaseOrderDetailRepository->add($purchaseOrderDetail);
                } else {
                    $this->purchaseOrderPaperDetailRepository->add($purchaseOrderPaperDetail);
                }
            }
            $this->addInventories($receiveHeader);
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($receiveHeader);
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

    private function getPurchaseRequestDetailForMaterialOrPaper(ReceiveDetail $receiveDetail)
    {
        $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
        $purchaseOrderPaperDetail = $receiveDetail->getPurchaseOrderPaperDetail();
        if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail === null) {
            return null;
        } else if ($purchaseOrderPaperDetail === null && $purchaseOrderDetail !== null) {
            return $purchaseOrderDetail->getPurchaseRequestDetail();
        } else if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail !== null) {
            return $purchaseOrderPaperDetail->getPurchaseRequestPaperDetail();
        }
    }

    private function setMaterialOrPaper(ReceiveDetail $receiveDetail, $purchaseOrderDetailForMaterialOrPaper): void
    {
        $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
        $purchaseOrderPaperDetail = $receiveDetail->getPurchaseOrderPaperDetail();
        if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail === null) {
            $receiveDetail->setMaterial(null);
            $receiveDetail->setPaper(null);
        } else if ($purchaseOrderPaperDetail === null && $purchaseOrderDetail !== null) {
            $receiveDetail->setMaterial($purchaseOrderDetailForMaterialOrPaper->getMaterial());
            $receiveDetail->setPaper(null);
        } else if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail !== null) {
            $receiveDetail->setMaterial(null);
            $receiveDetail->setPaper($purchaseOrderDetailForMaterialOrPaper->getPaper());
        }
    }

    private function addInventories(ReceiveHeader $receiveHeader): void
    {
        InventoryUtil::reverseOldData($this->inventoryRepository, $receiveHeader);
        
        $receiveDetails = $receiveHeader->getReceiveDetails()->toArray();
        if (!empty($receiveDetails[0]->getMaterial())) {
            $averagePriceList = InventoryUtil::getAveragePriceList('material', $this->purchaseOrderDetailRepository, $receiveDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $receiveHeader, $receiveDetails, function($newInventory, $receiveDetail) use ($averagePriceList, $receiveHeader) {
                $material = $receiveDetail->getMaterial();
                $purchasePrice = isset($averagePriceList[$material->getId()]) ? $averagePriceList[$material->getId()] : '0.00';
                $newInventory->setTransactionSubject($receiveDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setMaterial($material);
                $newInventory->setWarehouse($receiveHeader->getWarehouse());
                $newInventory->setInventoryMode('material');
                $newInventory->setQuantityIn($receiveDetail->getReceivedQuantity());
            });
        } else if (!empty($receiveDetails[0]->getPaper())) {
            $averagePriceList = InventoryUtil::getAveragePriceList('paper', $this->purchaseOrderPaperDetailRepository, $receiveDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $receiveHeader, $receiveDetails, function($newInventory, $receiveDetail) use ($averagePriceList, $receiveHeader) {
                $paper = $receiveDetail->getPaper();
                $purchasePrice = isset($averagePriceList[$paper->getId()]) ? $averagePriceList[$paper->getId()] : '0.00';
                $newInventory->setTransactionSubject($receiveDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setPaper($paper);
                $newInventory->setWarehouse($receiveHeader->getWarehouse());
                $newInventory->setInventoryMode('paper');
                $newInventory->setQuantityIn($receiveDetail->getReceivedQuantity());
            });
        }
    }
}
