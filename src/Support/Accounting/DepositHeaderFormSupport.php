<?php

namespace App\Support\Accounting;

use App\Entity\Accounting\DepositHeader;
use App\Support\SupportEntityBuilder;

trait DepositHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(DepositHeader $depositHeader): array
    {
        $account = $depositHeader->getAccount();
        return [
            'codeNumber' => $depositHeader->getCodeNumber(),
            'transactionDate' => $depositHeader->getTransactionDate(),
            'note' => $depositHeader->getNote(),
            'account' => [
                'name' => $account->getName(),
                'code' => $account->getCode(),
            ],
            'totalAmount' => $depositHeader->getTotalAmount(),
            'depositDetails' => array_map(function($depositDetail) {
                return [
                    'account' => [
                        'code' => $depositDetail->getAccount()->getCode(),
                        'name' => $depositDetail->getAccount()->getName(),
                    ],
                    'description' => $depositDetail->getDescription(),
                    'amount' => $depositDetail->getAmount(),
                    'memo' => $depositDetail->getMemo(),
                ];
            }, $depositHeader->getDepositDetails()->toArray()),
        ];
    }
}
