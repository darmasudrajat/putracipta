<?php

namespace App\Sync\Production;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Production\ProductPrototypeDetail;
use App\Entity\Production\ProductPrototypePilotDetail;
use App\Entity\Production\ProductPrototype;

class ProductPrototypeFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(ProductPrototype::class);
        $this->setupAssociations(ProductPrototypeDetail::class);
        $this->setupAssociations(ProductPrototypePilotDetail::class);
    }
}
