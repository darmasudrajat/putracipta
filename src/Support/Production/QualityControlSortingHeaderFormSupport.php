<?php

namespace App\Support\Production;

use App\Entity\Production\QualityControlSortingHeader;
use App\Support\SupportEntityBuilder;

trait QualityControlSortingHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(QualityControlSortingHeader $qualityControlSortingHeader): array
    {
        return [
            'codeNumber' => $qualityControlSortingHeader->getCodeNumber(),
            'transactionDate' => $qualityControlSortingHeader->getTransactionDate(),
            'note' => $qualityControlSortingHeader->getNote(),
        ];
    }
}
