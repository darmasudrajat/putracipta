<?php

namespace App\Sync\Production;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Production\ProductDevelopmentDetail;
use App\Entity\Production\ProductDevelopment;

class ProductDevelopmentFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(ProductDevelopment::class);
        $this->setupAssociations(ProductDevelopmentDetail::class);
    }
}
