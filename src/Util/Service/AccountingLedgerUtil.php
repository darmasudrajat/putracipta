<?php

namespace App\Util\Service;

use App\Entity\Accounting\AccountingLedger;

class AccountingLedgerUtil
{
    public static function reverseOldData($repository, $formDataEntity): void
    {
        $lastAccountingLedgerItems = $repository->findBy([
            'transactionCodeNumberOrdinal' => $formDataEntity->getCodeNumberOrdinal(),
            'transactionCodeNumberMonth' => $formDataEntity->getCodeNumberMonth(),
            'transactionCodeNumberYear' => $formDataEntity->getCodeNumberYear(),
            'transactionType' => $formDataEntity->getCodeNumberConstant(),
            'isReversed' => false,
        ]);
        foreach ($lastAccountingLedgerItems as $lastAccountingLedgerItem) {
            $lastAccountingLedgerItem->setIsReversed(true);
            $repository->add($lastAccountingLedgerItem);
            $reversedAccountingLedger = new AccountingLedger();
            $reversedAccountingLedger->setTransactionCodeNumberOrdinal($lastAccountingLedgerItem->getTransactionCodeNumberOrdinal());
            $reversedAccountingLedger->setTransactionCodeNumberMonth($lastAccountingLedgerItem->getTransactionCodeNumberMonth());
            $reversedAccountingLedger->setTransactionCodeNumberYear($lastAccountingLedgerItem->getTransactionCodeNumberYear());
            $reversedAccountingLedger->setTransactionDate($lastAccountingLedgerItem->getTransactionDate());
            $reversedAccountingLedger->setTransactionType($lastAccountingLedgerItem->getTransactionType());
            $reversedAccountingLedger->setTransactionSubject($lastAccountingLedgerItem->getTransactionSubject());
            $reversedAccountingLedger->setAccount($lastAccountingLedgerItem->getAccount());
            $reversedAccountingLedger->setCreatedAccountingLedgerDateTime($lastAccountingLedgerItem->getCreatedAccountingLedgerDateTime());
            $reversedAccountingLedger->setNote($lastAccountingLedgerItem->getNote());
            $reversedAccountingLedger->setDebitAmount(-($lastAccountingLedgerItem->getDebitAmount()));
            $reversedAccountingLedger->setCreditAmount(-($lastAccountingLedgerItem->getCreditAmount()));
            $reversedAccountingLedger->setIsReversed(true);
            $repository->add($reversedAccountingLedger);
        }
    }

    public static function addNewData($accountingLedgerRepository, $formDataEntity, array $formDataEntityDetails, callable $setDataFunction): void
    {
        foreach ($formDataEntityDetails as $formDataEntityDetail) {
            if (!$formDataEntityDetail->isIsCanceled()) {
                $newAccountingLedger = new AccountingLedger();
                $newAccountingLedger->setTransactionCodeNumberOrdinal($formDataEntity->getCodeNumberOrdinal());
                $newAccountingLedger->setTransactionCodeNumberMonth($formDataEntity->getCodeNumberMonth());
                $newAccountingLedger->setTransactionCodeNumberYear($formDataEntity->getCodeNumberYear());
                $newAccountingLedger->setTransactionDate($formDataEntity->getTransactionDate());
                $newAccountingLedger->setTransactionType($formDataEntity->getCodeNumberConstant());
                $newAccountingLedger->setNote($formDataEntity->getNote());
                $newAccountingLedger->setCreatedAccountingLedgerDateTime(new \DateTime());
                $setDataFunction($newAccountingLedger, $formDataEntityDetail);
                $accountingLedgerRepository->add($newAccountingLedger);
            }
        }
    }
}
