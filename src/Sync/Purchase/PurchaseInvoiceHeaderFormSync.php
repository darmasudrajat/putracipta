<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\PurchaseInvoiceDetail;
use App\Entity\Purchase\PurchaseInvoiceHeader;

class PurchaseInvoiceHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(PurchaseInvoiceHeader::class);
        $this->setupAssociations(PurchaseInvoiceDetail::class);
    }
    
    public function syncCodeNumber($purchaseInvoiceHeader, $purchaseInvoiceHeaderRepository)
    {
        if ($purchaseInvoiceHeader->getTransactionDate() !== null && $purchaseInvoiceHeader->getId() === null) {
            $year = $purchaseInvoiceHeader->getTransactionDate()->format('y');
            $month = $purchaseInvoiceHeader->getTransactionDate()->format('m');
            $lastPurchaseInvoiceHeader = $purchaseInvoiceHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseInvoiceHeader = ($lastPurchaseInvoiceHeader === null) ? $purchaseInvoiceHeader : $lastPurchaseInvoiceHeader;
            $purchaseInvoiceHeader->setCodeNumberToNext($currentPurchaseInvoiceHeader->getCodeNumber(), $year, $month);
        }
    }
    
    public function syncInvoiceSummaryAndReference($purchaseInvoiceHeader)
    {
        $receiveHeader = $purchaseInvoiceHeader->getReceiveHeader();
        if ($receiveHeader !== null) {
            $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader() === null ? $receiveHeader->getPurchaseOrderPaperHeader() : $receiveHeader->getPurchaseOrderHeader();
            $purchaseInvoiceHeader->setDiscountValueType($purchaseOrderHeader === null ? PurchaseInvoiceHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE : $purchaseOrderHeader->getDiscountValueType());
            $purchaseInvoiceHeader->setDiscountValue($purchaseOrderHeader === null ? '0.00' : $purchaseOrderHeader->getDiscountValue());
            $purchaseInvoiceHeader->setTaxMode($purchaseOrderHeader === null ? PurchaseInvoiceHeader::TAX_MODE_NON_TAX : $purchaseOrderHeader->getTaxMode());
            $purchaseInvoiceHeader->setTaxPercentage($purchaseOrderHeader === null ? 0 : $purchaseOrderHeader->getTaxPercentage());
        }
        
        $purchaseInvoiceHeader->setSupplier($receiveHeader === null ? null : $receiveHeader->getSupplier());
        $purchaseInvoiceHeader->setDueDate($purchaseInvoiceHeader->getSyncDueDate());
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $purchaseInvoiceDetail->setIsCanceled($purchaseInvoiceDetail->getSyncIsCanceled());
        }
        
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $receiveDetail = $purchaseInvoiceDetail->getReceiveDetail();
            $purchaseOrderDetail = empty($receiveDetail->getPurchaseOrderDetail()) ? $receiveDetail->getPurchaseOrderPaperDetail(): $receiveDetail->getPurchaseOrderDetail();
            $purchaseInvoiceDetail->setMaterial($receiveDetail->getMaterial());
            $purchaseInvoiceDetail->setPaper($receiveDetail->getPaper());
            $purchaseInvoiceDetail->setQuantity($receiveDetail->getReceivedQuantity());
            $purchaseInvoiceDetail->setUnitPrice($purchaseOrderDetail->getUnitPriceBeforeTax());
            $purchaseInvoiceDetail->setUnit($receiveDetail === null ? null : $receiveDetail->getUnit());
        }
        
        $purchaseInvoiceHeader->setSubTotal($purchaseInvoiceHeader->getSyncSubTotal());
        $purchaseInvoiceHeader->setTaxNominal($purchaseInvoiceHeader->getSyncTaxNominal());
        $purchaseInvoiceHeader->setGrandTotal($purchaseInvoiceHeader->getSyncGrandTotal());
    }
    
    public function syncTotalReturn($purchaseInvoiceHeader)
    {
        $receiveHeader = $purchaseInvoiceHeader->getReceiveHeader();
        $purchaseReturnHeaders = $receiveHeader === null ? null : $receiveHeader->getPurchaseReturnHeaders();
        if ($purchaseReturnHeaders !== null) {
            foreach ($purchaseReturnHeaders as $purchaseReturnHeader) {
                if ($purchaseReturnHeader->isIsProductExchange() === false) {
                    $purchaseInvoiceHeader->setTotalReturn($purchaseReturnHeader->getGrandTotal());
                }
            }
        }
    }
    
    public function syncRemaining($purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
    }
}
