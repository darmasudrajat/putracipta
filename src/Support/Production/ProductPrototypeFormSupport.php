<?php

namespace App\Support\Production;

use App\Entity\Production\ProductPrototype;
use App\Support\SupportEntityBuilder;

trait ProductPrototypeFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(ProductPrototype $productPrototype): array
    {
        return [
            'codeNumber' => $productPrototype->getCodeNumber(),
            'transactionDate' => $productPrototype->getTransactionDate(),
            'note' => $productPrototype->getNote(),
        ];
    }
}
