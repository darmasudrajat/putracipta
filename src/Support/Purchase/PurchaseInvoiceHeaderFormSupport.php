<?php

namespace App\Support\Purchase;

use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Support\SupportEntityBuilder;

trait PurchaseInvoiceHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(PurchaseInvoiceHeader $purchaseInvoiceHeader): array
    {
        $supplier = $purchaseInvoiceHeader->getSupplier();
        return [
            'codeNumber' => $purchaseInvoiceHeader->getCodeNumber(),
            'transactionDate' => $purchaseInvoiceHeader->getTransactionDate(),
            'supplier' => [
                'name' => $supplier->getName(),
                'company' => $supplier->getCompany(),
            ],
            'purchaseInvoiceDetails' => array_map(function($purchaseInvoiceDetail) {
                $material = $purchaseInvoiceDetail->getMaterial();
                $paper = $purchaseInvoiceDetail->getPaper();
                return [
                    'material' => [
                        'code' => $material === null ? '' : $material->getCode(),
                        'name' => $material === null ? '' : $material->getName(),
                    ],
                    'paper' => [
                        'code' => $paper === null ? '' : $paper->getCode(),
                        'name' => $paper === null ? '' : $paper->getName(),
                    ],
                    'quantity' => $purchaseInvoiceDetail->getQuantity(),
                    'unitPrice' => $purchaseInvoiceDetail->getUnitPrice(),
                ];
            }, $purchaseInvoiceHeader->getPurchaseInvoiceDetails()->toArray()),
        ];
    }
}
