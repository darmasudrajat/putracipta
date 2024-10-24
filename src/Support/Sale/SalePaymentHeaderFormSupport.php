<?php

namespace App\Support\Sale;

use App\Entity\Sale\SalePaymentHeader;
use App\Support\SupportEntityBuilder;

trait SalePaymentHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(SalePaymentHeader $salePaymentHeader): array
    {
        $customer = $salePaymentHeader->getCustomer();
        return [
            'codeNumber' => $salePaymentHeader->getCodeNumber(),
            'transactionDate' => $salePaymentHeader->getTransactionDate(),
            'paymentType' => $salePaymentHeader->getPaymentType()->getName(),
            'totalAmount' => $salePaymentHeader->getTotalAmount(),
            'referenceNumber' => $salePaymentHeader->getReferenceNumber(),
            'referenceDate' => $salePaymentHeader->getReferenceDate(),
            'administrationFee' => $salePaymentHeader->getAdministrationFee(),
            'receivedAmount' => $salePaymentHeader->getReceivedAmount(),
            'returnAmount' => $salePaymentHeader->getReturnAmount(),
            'saleOrderReferenceNumbers' => $salePaymentHeader->getSaleOrderReferenceNumbers(),
            'note' => $salePaymentHeader->getNote(),
            'customer' => [
                'name' => $customer->getName(),
                'company' => $customer->getCompany(),
            ],
            'salePaymentDetails' => array_map(function($salePaymentDetail) {
                $account = $salePaymentDetail->getAccount();
                return [
                    'account' => [
                        'code' => $account->getCode(),
                        'name' => $account->getName(),
                    ],
                    'saleInvoiceHeader' => $salePaymentDetail->getSaleInvoiceHeader()->getCodeNumber(),
                    'amount' => $salePaymentDetail->getAmount(),
                    'memo' => $salePaymentDetail->getMemo(),
                    'serviceTaxPercentage' => $salePaymentDetail->getServiceTaxPercentage(),
                    'serviceTaxNominal' => $salePaymentDetail->getServiceTaxNominal(),
                ];
            }, $salePaymentHeader->getSalePaymentDetails()->toArray()),
        ];
    }
}
