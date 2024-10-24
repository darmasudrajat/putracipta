<?php

namespace App\Support\Sale;

use App\Entity\Sale\DeliveryHeader;
use App\Support\SupportEntityBuilder;

trait DeliveryHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(DeliveryHeader $deliveryHeader): array
    {
        $customer = $deliveryHeader->getCustomer();
        $warehouse = $deliveryHeader->getWarehouse();
        return [
            'codeNumber' => $deliveryHeader->getCodeNumber(),
            'transactionDate' => $deliveryHeader->getTransactionDate(),
            'customer' => [
                'name' => $customer->getName(),
                'company' => $customer->getCompany(),
            ],
            'warehouse' => [
                'name' => $warehouse->getName(),
                'code' => $warehouse->getCode(),
            ],
            'saleOrderReferenceNumbers' => $deliveryHeader->getSaleOrderReferenceNumbers(),
            'fscCode' => $deliveryHeader->isIsUsingFscPaper(),
            'vehicleName' => $deliveryHeader->getVehicleName(),
            'vehiclePlateNumber' => $deliveryHeader->getVehiclePlateNumber(),
            'vehicleDriverName' => $deliveryHeader->getVehicleDriverName(),
            'note' => $deliveryHeader->getNote(),
            'deliveryDetails' => array_map(function($deliveryDetail) {
                return [
                    'product' => [
                        'code' => $deliveryDetail->getProduct()->getCode(),
                        'name' => $deliveryDetail->getProduct()->getName(),
                    ],
                    'saleOrderDetailId' => $deliveryDetail->getSaleOrderDetail(),
                    'deliveredQuantity' => $deliveryDetail->getDeliveredQuantity(),
                    'packaging' => $deliveryDetail->getPackaging(),
                    'fscCode' => $deliveryDetail->getFscCode(),
                    'quantity' => $deliveryDetail->getQuantity(),
                    'masterOrderProductDetailId' => $deliveryDetail->getMasterOrderProductDetail(),
                ];
            }, $deliveryHeader->getDeliveryDetails()->toArray()),
        ];
    }
}
