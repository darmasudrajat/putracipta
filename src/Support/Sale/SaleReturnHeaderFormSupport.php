<?php

namespace App\Support\Sale;

use App\Entity\Sale\SaleReturnHeader;
use App\Support\SupportEntityBuilder;

trait SaleReturnHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(SaleReturnHeader $saleReturnHeader): array
    {
        $customer = $saleReturnHeader->getCustomer();
        $warehouse = $saleReturnHeader->getWarehouse();
        return [
            'codeNumber' => $saleReturnHeader->getCodeNumber(),
            'transactionDate' => $saleReturnHeader->getTransactionDate(),
            'deliveryHeader' => $saleReturnHeader->getDeliveryHeader(),
            'taxPercentage' => $saleReturnHeader->getTaxPercentage(),
            'taxNominal' => $saleReturnHeader->getTaxNominal(),
            'subTotal' => $saleReturnHeader->getSubTotal(),
            'grandTotal' => $saleReturnHeader->getGrandTotal(),
            'saleOrderReferenceNumber' => $saleReturnHeader->getSaleOrderReferenceNumbers(),
            'note' => $saleReturnHeader->getNote(),
            'customer' => [
                'name' => $customer->getName(),
                'company' => $customer->getCompany(),
            ],
            'warehouse' => [
                'name' => $warehouse->getName(),
                'code' => $warehouse->getCode(),
            ],
            'saleReturnDetails' => array_map(function($saleReturnDetail) {
                $product = $saleReturnDetail->getProduct();
                return [
                    'product' => [
                        'code' => $product->getCode(),
                        'name' => $product->getName(),
                    ],
                    'unitName' => $saleReturnDetail->getUnit()->getName(),
                    'deliveryDetail' => $saleReturnDetail->getDeliveryDetail(),
                    'quantity' => $saleReturnDetail->getQuantity(),
                    'unitPrice' => $saleReturnDetail->getUnitPrice(),
                ];
            }, $saleReturnHeader->getSaleReturnDetails()->toArray()),
        ];
    }
}
