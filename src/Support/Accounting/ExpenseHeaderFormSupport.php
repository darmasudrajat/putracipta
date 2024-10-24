<?php

namespace App\Support\Accounting;

use App\Entity\Accounting\ExpenseHeader;
use App\Support\SupportEntityBuilder;

trait ExpenseHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(ExpenseHeader $expenseHeader): array
    {
        $account = $expenseHeader->getAccount();
        return [
            'codeNumber' => $expenseHeader->getCodeNumber(),
            'transactionDate' => $expenseHeader->getTransactionDate(),
            'note' => $expenseHeader->getNote(),
            'account' => [
                'name' => $account->getName(),
                'code' => $account->getCode(),
            ],
            'totalAmount' => $expenseHeader->getTotalAmount(),
            'expenseDetails' => array_map(function($expenseDetail) {
                return [
                    'account' => [
                        'code' => $expenseDetail->getAccount()->getCode(),
                        'name' => $expenseDetail->getAccount()->getName(),
                    ],
                    'description' => $expenseDetail->getDescription(),
                    'amount' => $expenseDetail->getAmount(),
                    'memo' => $expenseDetail->getMemo(),
                ];
            }, $expenseHeader->getExpenseDetails()->toArray()),
        ];
    }
}
