<?php

namespace App\Support\Purchase;

use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Support\SupportEntityBuilder;

trait PurchaseOrderPaperHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(PurchaseOrderPaperHeader $purchaseOrderPaperHeader): array
    {
        $supplier = $purchaseOrderPaperHeader->getSupplier();
        return [
            'codeNumber' => $purchaseOrderPaperHeader->getCodeNumber(),
            'transactionDate' => $purchaseOrderPaperHeader->getTransactionDate(),
            'supplier' => [
                'name' => $supplier->getName(),
                'company' => $supplier->getCompany(),
            ],
            'purchaseOrderPaperDetails' => array_map(function($purchaseOrderPaperDetail) {
                $paper = $purchaseOrderPaperDetail->getPaper();
                return [
                    'paper' => [
                        'code' => $paper->getCode(),
                        'name' => $paper->getName(),
                    ],
                    'quantity' => $purchaseOrderPaperDetail->getQuantity(),
                    'unitPrice' => $purchaseOrderPaperDetail->getUnitPrice(),
                ];
            }, $purchaseOrderPaperHeader->getPurchaseOrderPaperDetails()->toArray()),
        ];
    }
}
