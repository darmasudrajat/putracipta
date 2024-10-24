<?php

namespace App\Support\Purchase;

use App\Entity\Purchase\ReceiveHeader;
use App\Support\SupportEntityBuilder;

trait ReceiveHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(ReceiveHeader $receiveHeader): array
    {
        $supplier = $receiveHeader->getSupplier();
        $warehouse = $receiveHeader->getWarehouse();
        $purchaseOrder = $receiveHeader->getPurchaseOrderHeader() === null ? $receiveHeader->getPurchaseOrderPaperHeader() : $receiveHeader->getPurchaseOrderHeader();
        return [
            'codeNumber' => $receiveHeader->getCodeNumber(),
            'transactionDate' => $receiveHeader->getTransactionDate(),
            'supplier' => [
                'name' => $supplier->getName(),
                'company' => $supplier->getCompany(),
            ],
            'warehouse' => [
                'name' => $warehouse->getName(),
                'code' => $warehouse->getCode(),
            ],
            'supplierDeliveryCodeNumber' => $receiveHeader->getSupplierDeliveryCodeNumber(),
            'purchaseOrderCodeNumber' => $purchaseOrder->getCodeNumber(),
            'note' => $receiveHeader->getNote(),
            'receiveDetails' => array_map(function($receiveDetail) {
                return [
                    'item' => [
                        'code' => $receiveDetail->getMaterial() === null ? $receiveDetail->getPaper()->getCodeNumber() : $receiveDetail->getMaterial()->getCode(),
                        'name' => $receiveDetail->getMaterial() === null ? $receiveDetail->getPaper()->getPaperNameSizeCombination() : $receiveDetail->getMaterial()->getName(),
                    ],
                    'receivedQuantity' => $receiveDetail->getReceivedQuantity(),
                    'remainingQuantity' => $receiveDetail->getRemainingQuantity(),
                    'memo' => $receiveDetail->getMemo(),
                ];
            }, $receiveHeader->getReceiveDetails()->toArray()),
        ];
    }
}
