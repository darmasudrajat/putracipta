<?php

namespace App\Sync\Accounting;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Accounting\DepositDetail;
use App\Entity\Accounting\DepositHeader;

class DepositHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(DepositHeader::class);
        $this->setupAssociations(DepositDetail::class);
    }
}
