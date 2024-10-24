<?php

namespace App\Sync\Stock;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Stock\InventoryReleaseMaterialDetail;
use App\Entity\Stock\InventoryReleasePaperDetail;
use App\Entity\Stock\InventoryReleaseHeader;

class InventoryReleaseHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(InventoryReleaseHeader::class);
        $this->setupAssociations(InventoryReleaseMaterialDetail::class);
        $this->setupAssociations(InventoryReleasePaperDetail::class);
    }
}
