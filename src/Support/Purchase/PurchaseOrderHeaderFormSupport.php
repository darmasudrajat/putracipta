<?php

namespace App\Support\Purchase;

use App\Entity\Purchase\PurchaseOrderHeader;
use App\Support\SupportEntityBuilder;

trait PurchaseOrderHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(PurchaseOrderHeader $purchaseOrderHeader): array
    {
        $supplier = $purchaseOrderHeader->getSupplier();
        return [
            'codeNumber' => $purchaseOrderHeader->getCodeNumber(),
            'transactionDate' => $purchaseOrderHeader->getTransactionDate(),
            'supplier' => [
                'name' => $supplier->getName(),
                'company' => $supplier->getCompany(),
            ],
            'purchaseOrderDetails' => array_map(function($purchaseOrderDetail) {
                $material = $purchaseOrderDetail->getMaterial();
                return [
                    'material' => [
                        'code' => $material->getCode(),
                        'name' => $material->getName(),
                    ],
                    'quantity' => $purchaseOrderDetail->getQuantity(),
                    'unitPrice' => $purchaseOrderDetail->getUnitPrice(),
                ];
            }, $purchaseOrderHeader->getPurchaseOrderDetails()->toArray()),
        ];
    }
}
