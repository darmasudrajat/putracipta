<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;

class PurchaseOrderHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(PurchaseOrderHeader::class);
        $this->setupAssociations(PurchaseOrderDetail::class);
    }
    
    public function syncCodeNumber($purchaseOrderHeader, $purchaseOrderHeaderRepository)
    {
        if ($purchaseOrderHeader->getTransactionDate() !== null && $purchaseOrderHeader->getId() === null) {
            $year = $purchaseOrderHeader->getTransactionDate()->format('y');
            $month = $purchaseOrderHeader->getTransactionDate()->format('m');
            $lastPurchaseOrderHeader = $purchaseOrderHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseOrderHeader = ($lastPurchaseOrderHeader === null) ? $purchaseOrderHeader : $lastPurchaseOrderHeader;
            $purchaseOrderHeader->setCodeNumberToNext($currentPurchaseOrderHeader->getCodeNumber(), $year, $month);
        }
    }
    
    public function syncOrderSummary($purchaseOrderHeader, $vatPercentage)
    {
        if ($purchaseOrderHeader->getTaxMode() !== $purchaseOrderHeader::TAX_MODE_NON_TAX) {
            $purchaseOrderHeader->setTaxPercentage($vatPercentage);
        } else {
            $purchaseOrderHeader->setTaxPercentage(0);
        }
        
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setUnitPriceBeforeTax($purchaseOrderDetail->getSyncUnitPriceBeforeTax());
            $purchaseOrderDetail->setTotal($purchaseOrderDetail->getSyncTotal());
        }
        $supplier = $purchaseOrderHeader->getSupplier();
        $purchaseOrderHeader->setCurrency($supplier === null ? null : $supplier->getCurrency());
        $purchaseOrderHeader->setSubTotal($purchaseOrderHeader->getSyncSubTotal());
        $purchaseOrderHeader->setTaxNominal($purchaseOrderHeader->getSyncTaxNominal());
        $purchaseOrderHeader->setGrandTotal($purchaseOrderHeader->getSyncGrandTotal());
    }
    
    public function syncOrderRemainingAndClosedInfo($purchaseOrderHeader)
    {
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setRemainingReceive($purchaseOrderDetail->getSyncRemainingReceive());
            
            if ($purchaseOrderDetail->getRemainingReceive() === 0) {
                $purchaseOrderDetail->setIsTransactionClosed(true);
            } else if ($purchaseOrderDetail->isIsTransactionClosed() === true) {
                $purchaseOrderDetail->setRemainingReceive(0);
            }
        }
        $purchaseOrderHeader->setTotalRemainingReceive($purchaseOrderHeader->getSyncTotalRemainingReceive());
    }
    
    public function syncInvoiceSummary($purchaseOrderHeader)
    {
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            foreach ($purchaseOrderDetail->getReceiveDetails() as $receiveDetail) {
                foreach ($receiveDetail->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
                    $purchaseInvoiceHeader = $purchaseInvoiceDetail->getPurchaseInvoiceHeader();
                    $purchaseInvoiceDetail->setUnitPrice($purchaseOrderDetail->getUnitPriceBeforeTax());
                    $purchaseInvoiceHeader->setSubTotal($purchaseInvoiceHeader->getSyncSubTotal());
                    $purchaseInvoiceHeader->setDiscountValueType($purchaseOrderHeader->getDiscountValueType());
                    $purchaseInvoiceHeader->setDiscountValue($purchaseOrderHeader->getDiscountValue());
                    $purchaseInvoiceHeader->setTaxMode($purchaseOrderHeader->getTaxMode());
                    $purchaseInvoiceHeader->setTaxPercentage($purchaseOrderHeader->getTaxPercentage());
                    $purchaseInvoiceHeader->setTaxNominal($purchaseInvoiceHeader->getSyncTaxNominal());
                    $purchaseInvoiceHeader->setGrandTotal($purchaseInvoiceHeader->getSyncGrandTotal());
                }
            }
        }
    }
    
    public function syncRequestStatus($purchaseOrderHeader)
    {
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseRequestDetail = $purchaseOrderDetail->getPurchaseRequestDetail();
            if ($purchaseRequestDetail !== null && $purchaseOrderHeader->getId() === null) {
                $purchaseRequestDetail->setTransactionStatus(PurchaseRequestDetail::TRANSACTION_STATUS_PURCHASE);
            }
        }
    }
}
