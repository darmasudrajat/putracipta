<?php

namespace App\Support\Sale;

use App\Entity\Sale\SaleOrderHeader;
use App\Support\SupportEntityBuilder;

trait SaleOrderHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(SaleOrderHeader $saleOrderHeader): array
    {
        $customer = $saleOrderHeader->getCustomer();
        return [
            'codeNumber' => $saleOrderHeader->getCodeNumber(),
            'transactionDate' => $saleOrderHeader->getTransactionDate(),
            'referenceNumber' => $saleOrderHeader->getReferenceNumber(),
            'transactionStatus' => $saleOrderHeader->getTransactionStatus(),
            'taxPercentage' => $saleOrderHeader->getTaxPercentage(),
            'taxNominal' => $saleOrderHeader->getTaxNominal(),
            'subTotal' => $saleOrderHeader->getSubTotal(),
            'grandTotal' => $saleOrderHeader->getGrandTotal(),
            'fscCode' => $saleOrderHeader->isIsUsingFscPaper(),
            'note' => $saleOrderHeader->getNote(),
            'customer' => [
                'name' => $customer->getName(),
                'company' => $customer->getCompany(),
            ],
            'saleOrderDetails' => array_map(function($saleOrderDetail) {
                $product = $saleOrderDetail->getProduct();
                return [
                    'product' => [
                        'code' => $product->getCode(),
                        'name' => $product->getName(),
                    ],
                    'unitName' => $saleOrderDetail->getUnit()->getName(),
                    'deliveryDate' => $saleOrderDetail->getDeliveryDate(),
                    'quantity' => $saleOrderDetail->getQuantity(),
                    'unitPrice' => $saleOrderDetail->getUnitPrice(),
                    'unitPriceBeforeTax' => $saleOrderDetail->getUnitPriceBeforeTax(),
                ];
            }, $saleOrderHeader->getSaleOrderDetails()->toArray()),
        ];
    }
}
