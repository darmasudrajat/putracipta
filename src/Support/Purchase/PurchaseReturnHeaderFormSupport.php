<?php

namespace App\Support\Purchase;

use App\Entity\Purchase\PurchaseReturnHeader;
use App\Support\SupportEntityBuilder;

trait PurchaseReturnHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(PurchaseReturnHeader $purchaseReturnHeader): array
    {
        $supplier = $purchaseReturnHeader->getSupplier();
        $warehouse = $purchaseReturnHeader->getWarehouse();
        return [
            'codeNumber' => $purchaseReturnHeader->getCodeNumber(),
            'transactionDate' => $purchaseReturnHeader->getTransactionDate(),
            'receiveHeaderCodeNumber' => $purchaseReturnHeader->getReceiveHeader()->getCodeNumber(),
            'taxPercentage' => $purchaseReturnHeader->getTaxPercentage(),
            'taxNominal' => $purchaseReturnHeader->getTaxNominal(),
            'subTotal' => $purchaseReturnHeader->getSubTotal(),
            'grandTotal' => $purchaseReturnHeader->getGrandTotal(),
            'productExchange' => $purchaseReturnHeader->isIsProductExchange(),
            'note' => $purchaseReturnHeader->getNote(),
            'supplier' => [
                'name' => $supplier->getName(),
                'company' => $supplier->getCompany(),
            ],
            'warehouse' => [
                'name' => $warehouse->getName(),
                'code' => $warehouse->getCode(),
            ],
            'purchaseReturnDetails' => array_map(function($purchaseReturnDetail) {
                return [
                    'item' => [
                        'code' => $purchaseReturnDetail->getMaterial() === null ? $purchaseReturnDetail->getPaper()->getCodeNumber() : $purchaseReturnDetail->getMaterial()->getCode(),
                        'name' => $purchaseReturnDetail->getMaterial() === null ? $purchaseReturnDetail->getPaper()->getPaperNameSizeCombination() : $purchaseReturnDetail->getMaterial()->getName(),
                    ],
                    'receiveDetailId' => $purchaseReturnDetail->getReceiveDetail(),
                    'quantity' => $purchaseReturnDetail->getQuantity(),
                    'unitPrice' => $purchaseReturnDetail->getUnitPrice(),
                ];
            }, $purchaseReturnHeader->getPurchaseReturnDetails()->toArray()),
        ];
    }
}
