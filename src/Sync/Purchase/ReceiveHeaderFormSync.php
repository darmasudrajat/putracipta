<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\ReceiveDetail;
use App\Entity\Purchase\ReceiveHeader;

class ReceiveHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(ReceiveHeader::class);
        $this->setupAssociations(ReceiveDetail::class);
    }
    
    public function syncCodeNumber($receiveHeader, $receiveHeaderRepository)
    {
        if ($receiveHeader->getTransactionDate() !== null && $receiveHeader->getId() === null) {
            $year = $receiveHeader->getTransactionDate()->format('y');
            $month = $receiveHeader->getTransactionDate()->format('m');
            $lastReceiveHeader = $receiveHeaderRepository->findRecentBy($year, $month);
            $currentReceiveHeader = ($lastReceiveHeader === null) ? $receiveHeader : $lastReceiveHeader;
            $receiveHeader->setCodeNumberToNext($currentReceiveHeader->getCodeNumber(), $year, $month);
        }
    }
    
    public function syncReceiveSummary($receiveHeader)
    {
        $receiveHeader->setTotalQuantity($receiveHeader->getSyncTotalQuantity());
    }
    
    public function syncOrderCodeNumber($receiveHeader)
    {
        $purchaseOrderHeaderForMaterialOrPaper = $this->getPurchaseOrderHeaderForMaterialOrPaper($receiveHeader);
        if ($purchaseOrderHeaderForMaterialOrPaper !== null) {
            $receiveHeader->setPurchaseOrderCodeNumberOrdinal($purchaseOrderHeaderForMaterialOrPaper->getCodeNumberOrdinal());
            $receiveHeader->setPurchaseOrderCodeNumberMonth($purchaseOrderHeaderForMaterialOrPaper->getCodeNumberMonth());
            $receiveHeader->setPurchaseOrderCodeNumberYear($purchaseOrderHeaderForMaterialOrPaper->getCodeNumberYear());
        }
    }
    
    public function syncReceiveReference($receiveHeader)
    {
        $purchaseOrderHeaderForMaterialOrPaper = $this->getPurchaseOrderHeaderForMaterialOrPaper($receiveHeader);
        $receiveHeader->setSupplier($purchaseOrderHeaderForMaterialOrPaper === null ? null : $purchaseOrderHeaderForMaterialOrPaper->getSupplier());
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $this->setMaterialOrPaper($receiveDetail, $purchaseOrderDetailForMaterialOrPaper);
            $receiveDetail->setUnit($purchaseOrderDetailForMaterialOrPaper === null ? null : $purchaseOrderDetailForMaterialOrPaper->getUnit());
        }
    }
    
    public function syncOrderRemainingAndClosedInfo($receiveHeader)
    {
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $receiveDetail->setRemainingReceive($receiveDetail->getSyncRemainingReceive());
            
            if ($receiveDetail->getRemainingReceive() === 0) {
                $receiveDetail->setIsTransactionClosed(true);
            } else if ($receiveDetail->isIsTransactionClosed() === true) {
                $receiveDetail->setRemainingReceive(0);
            }
        }
        $receiveHeader->setTotalRemainingReceive($receiveHeader->getSyncTotalRemainingReceive());
    }
    
    public function syncRemainingAndTransactionStatus($receiveHeader)
    {
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $oldReceiveDetails = empty($receiveDetail->getPurchaseOrderDetail()) ? $this->receiveDetailRepository->findByPurchaseOrderPaperDetail($purchaseOrderDetailForMaterialOrPaper) : $this->receiveDetailRepository->findByPurchaseOrderDetail($purchaseOrderDetailForMaterialOrPaper);
            $totalReceive = 0;
            foreach ($oldReceiveDetails as $oldReceiveDetail) {
                if ($oldReceiveDetail->getId() !== $receiveDetail->getId()) {
                    $totalReceive += $oldReceiveDetail->getReceivedQuantity();
                }
            }
            $totalReceive += $receiveDetail->getReceivedQuantity();
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
        
        $purchaseOrderHeaderForMaterialOrPaper = $this->getPurchaseOrderHeaderForMaterialOrPaper($receiveHeader);
        if ($purchaseOrderHeaderForMaterialOrPaper !== null) {
            if ($totalRemaining > 0) {
                $purchaseOrderHeaderForMaterialOrPaper->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_PARTIAL_RECEIVE);
            } else {
                $purchaseOrderHeaderForMaterialOrPaper->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_FULL_RECEIVE);
            }
            $purchaseOrderHeaderForMaterialOrPaper->setTotalRemainingReceive($purchaseOrderHeaderForMaterialOrPaper->getSyncTotalRemainingReceive());
        }
    }
    
    public function syncInvoiceSummary($receiveHeader)
    {
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            foreach ($receiveDetail->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
                $purchaseInvoiceHeader = $purchaseInvoiceDetail->getPurchaseInvoiceHeader();
                $purchaseInvoiceDetail->setQuantity($receiveDetail->getQuantity());
                $purchaseInvoiceHeader->setSubTotal($purchaseInvoiceHeader->getSyncSubTotal());
                $purchaseInvoiceHeader->setTaxNominal($purchaseInvoiceHeader->getSyncTaxNominal());
                $purchaseInvoiceHeader->setGrandTotal($purchaseInvoiceHeader->getSyncGrandTotal());
            }
        }
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
}
