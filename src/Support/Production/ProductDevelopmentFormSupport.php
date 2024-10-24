<?php

namespace App\Support\Production;

use App\Entity\Production\ProductDevelopment;
use App\Support\SupportEntityBuilder;

trait ProductDevelopmentFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(ProductDevelopment $productDevelopment): array
    {
        return [
            'codeNumber' => $productDevelopment->getCodeNumber(),
            'transactionDate' => $productDevelopment->getTransactionDate(),
            'note' => $productDevelopment->getNote(),
        ];
    }
}
