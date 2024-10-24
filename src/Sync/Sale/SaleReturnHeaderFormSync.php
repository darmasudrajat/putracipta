<?php

namespace App\Sync\Sale;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Sale\SaleReturnDetail;
use App\Entity\Sale\SaleReturnHeader;

class SaleReturnHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(SaleReturnHeader::class);
        $this->setupAssociations(SaleReturnDetail::class);
    }
}
