<?php

namespace App\Support\Purchase;

use App\Entity\Purchase\PurchaseRequestPaperHeader;
use App\Support\SupportEntityBuilder;

trait PurchaseRequestPaperHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(PurchaseRequestPaperHeader $purchaseRequestPaperHeader): array
    {
        $warehouse = $purchaseRequestPaperHeader->getWarehouse();
        return [
            'codeNumber' => $purchaseRequestPaperHeader->getCodeNumber(),
            'transactionDate' => $purchaseRequestPaperHeader->getTransactionDate(),
            'warehouse' => [
                'name' => $warehouse->getName(),
                'code' => $warehouse->getCode(),
            ],
            'purchaseRequestPaperDetails' => array_map(function($purchaseRequestPaperDetail) {
                $paper = $purchaseRequestPaperDetail->getPaper();
                return [
                    'paper' => [
                        'code' => $paper->getCode(),
                        'name' => $paper->getName(),
                    ],
                    'quantity' => $purchaseRequestPaperDetail->getQuantity(),
                    'usageDate' => $purchaseRequestPaperDetail->getUsageDate(),
                    'memo' => $purchaseRequestPaperDetail->getMemo(),
                    'transactionStatus' => $purchaseRequestPaperDetail->getTransactionStatus(),
                ];
            }, $purchaseRequestPaperHeader->getPurchaseRequestPaperDetails()->toArray()),
        ];
    }
}
