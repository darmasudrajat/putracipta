<?php

namespace App\Sync\Stock;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Stock\InventoryRequestMaterialDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use App\Entity\Stock\InventoryRequestHeader;

class InventoryRequestHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(InventoryRequestHeader::class);
        $this->setupAssociations(InventoryRequestMaterialDetail::class);
        $this->setupAssociations(InventoryRequestPaperDetail::class);
    }
}
