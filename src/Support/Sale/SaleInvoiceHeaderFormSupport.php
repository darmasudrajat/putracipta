<?php

namespace App\Support\Sale;

use App\Entity\Sale\SaleInvoiceHeader;
use App\Support\SupportEntityBuilder;

trait SaleInvoiceHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(SaleInvoiceHeader $saleInvoiceHeader): array
    {
        $customer = $saleInvoiceHeader->getCustomer();
        return [
            'codeNumber' => $saleInvoiceHeader->getCodeNumber(),
            'transactionDate' => $saleInvoiceHeader->getTransactionDate(),
            'invoiceTaxCodeNumber' => $saleInvoiceHeader->getInvoiceTaxCodeNumber(),
            'taxPercentage' => $saleInvoiceHeader->getTaxPercentage(),
            'taxNominal' => $saleInvoiceHeader->getTaxNominal(),
            'subTotal' => $saleInvoiceHeader->getSubTotal(),
            'grandTotal' => $saleInvoiceHeader->getGrandTotal(),
            'fscCode' => $saleInvoiceHeader->isIsUsingFscPaper(),
            'note' => $saleInvoiceHeader->getNote(),
            'dueDate' => $saleInvoiceHeader->getDueDate(),
            'invoiceTaxDate' => $saleInvoiceHeader->getInvoiceTaxDate(),
            'transactionStatus' => $saleInvoiceHeader->getTransactionStatus(),
            'serviceTaxPercentage' => $saleInvoiceHeader->getServiceTaxPercentage(),
            'serviceTaxNominal' => $saleInvoiceHeader->getServiceTaxNominal(),
            'saleOrderReferenceNumbers' => $saleInvoiceHeader->getSaleOrderReferenceNumbers(),
            'deliveryReferenceNumbers' => $saleInvoiceHeader->getDeliveryReferenceNumbers(),
            'customer' => [
                'name' => $customer->getName(),
                'company' => $customer->getCompany(),
            ],
            'saleInvoiceDetails' => array_map(function($saleInvoiceDetail) {
                $product = $saleInvoiceDetail->getProduct();
                return [
                    'product' => [
                        'code' => $product->getCode(),
                        'name' => $product->getName(),
                    ],
                    'quantity' => $saleInvoiceDetail->getQuantity(),
                    'unitPrice' => $saleInvoiceDetail->getUnitPrice(),
                    'returnAmount' => $saleInvoiceDetail->getReturnAmount(),
                    'deliveryDetail' => $saleInvoiceDetail->getDeliveryDetail(),
                ];
            }, $saleInvoiceHeader->getSaleInvoiceDetails()->toArray()),
        ];
    }
}
