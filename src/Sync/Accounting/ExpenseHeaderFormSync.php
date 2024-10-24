<?php

namespace App\Sync\Accounting;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Accounting\ExpenseDetail;
use App\Entity\Accounting\ExpenseHeader;

class ExpenseHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(ExpenseHeader::class);
        $this->setupAssociations(ExpenseDetail::class);
    }
}
