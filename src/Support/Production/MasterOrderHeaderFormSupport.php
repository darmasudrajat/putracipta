<?php

namespace App\Support\Production;

use App\Entity\Production\MasterOrderHeader;
use App\Support\SupportEntityBuilder;

trait MasterOrderHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(MasterOrderHeader $masterOrderHeader): array
    {
        return [
            'codeNumber' => $masterOrderHeader->getCodeNumber(),
            'transactionDate' => $masterOrderHeader->getTransactionDate(),
            'note' => $masterOrderHeader->getNote(),
        ];
    }
}
