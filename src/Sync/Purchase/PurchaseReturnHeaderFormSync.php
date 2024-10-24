<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\PurchaseReturnDetail;
use App\Entity\Purchase\PurchaseReturnHeader;

class PurchaseReturnHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(PurchaseReturnHeader::class);
        $this->setupAssociations(PurchaseReturnDetail::class);
    }
}
