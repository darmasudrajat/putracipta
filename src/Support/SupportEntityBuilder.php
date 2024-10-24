<?php

namespace App\Support;

use App\Entity\Support\TransactionLog;

trait SupportEntityBuilder
{
    private function buildTransactionLog($transactionEntity): TransactionLog
    {
        $currentDateTime = new \DateTime();
        $transactionLog = new TransactionLog();
        $transactionLog->setCodeNumberOrdinal($transactionEntity->getCodeNumberOrdinal());
        $transactionLog->setCodeNumberMonth($transactionEntity->getCodeNumberMonth());
        $transactionLog->setCodeNumberYear($transactionEntity->getCodeNumberYear());
        $transactionLog->setEntityName(get_class($transactionEntity));
        $transactionLog->setEntityId($transactionEntity->getId());
        $transactionLog->setTransactionDate($transactionEntity->getTransactionDate());
        $transactionLog->setLogDate($currentDateTime);
        $transactionLog->setLogTime($currentDateTime);
        $transactionLog->setNewData($this->transactionLogNewData($transactionEntity));
        return $transactionLog;
    }
}
