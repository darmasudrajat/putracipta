<?php

namespace App\Support\Purchase;

use App\Entity\Purchase\PurchaseRequestHeader;
use App\Support\SupportEntityBuilder;

trait PurchaseRequestHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(PurchaseRequestHeader $purchaseRequestHeader): array
    {
        $warehouse = $purchaseRequestHeader->getWarehouse();
        return [
            'codeNumber' => $purchaseRequestHeader->getCodeNumber(),
            'transactionDate' => $purchaseRequestHeader->getTransactionDate(),
            'warehouse' => [
                'name' => $warehouse->getName(),
                'code' => $warehouse->getCode(),
            ],
            'purchaseRequestDetails' => array_map(function($purchaseRequestDetail) {
                $material = $purchaseRequestDetail->getMaterial();
                return [
                    'material' => [
                        'code' => $material->getCode(),
                        'name' => $material->getName(),
                    ],
                    'quantity' => $purchaseRequestDetail->getQuantity(),
                    'usageDate' => $purchaseRequestDetail->getUsageDate(),
                    'memo' => $purchaseRequestDetail->getMemo(),
                    'transactionStatus' => $purchaseRequestDetail->getTransactionStatus(),
                ];
            }, $purchaseRequestHeader->getPurchaseRequestDetails()->toArray()),
        ];
    }
}
