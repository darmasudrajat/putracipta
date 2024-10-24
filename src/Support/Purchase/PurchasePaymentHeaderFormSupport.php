<?php

namespace App\Support\Purchase;

use App\Entity\Purchase\PurchasePaymentHeader;
use App\Support\SupportEntityBuilder;

trait PurchasePaymentHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(PurchasePaymentHeader $purchasePaymentHeader): array
    {
        $supplier = $purchasePaymentHeader->getSupplier();
        return [
            'codeNumber' => $purchasePaymentHeader->getCodeNumber(),
            'transactionDate' => $purchasePaymentHeader->getTransactionDate(),
            'paymentType' => $purchasePaymentHeader->getPaymentType()->getName(),
            'referenceNumber' => $purchasePaymentHeader->getReferenceNumber(),
            'supplierInvoiceCodeNumbers' => $purchasePaymentHeader->getSupplierInvoiceCodeNumbers(),
            'note' => $purchasePaymentHeader->getNote(),
            'supplier' => [
                'name' => $supplier->getName(),
                'company' => $supplier->getCompany(),
            ],
            'purchasePaymentDetails' => array_map(function($purchasePaymentDetail) {
                $account = $purchasePaymentDetail->getAccount();
                return [
                    'account' => [
                        'code' => $account->getCode(),
                        'name' => $account->getName(),
                    ],
                    'purchaseInvoiceCodeNumber' => $purchasePaymentDetail->getPurchaseInvoiceHeader()->getCodeNumber(),
                    'amount' => $purchasePaymentDetail->getAmount(),
                    'memo' => $purchasePaymentDetail->getMemo(),
                ];
            }, $purchasePaymentHeader->getPurchasePaymentDetails()->toArray()),
        ];
    }
}
