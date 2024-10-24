<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\Purchase\PurchaseRequestPaperHeader;

class PurchaseRequestPaperHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(PurchaseRequestPaperHeader::class);
        $this->setupAssociations(PurchaseRequestPaperDetail::class);
    }
}
