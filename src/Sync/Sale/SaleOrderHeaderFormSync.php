<?php

namespace App\Sync\Sale;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;

class SaleOrderHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(SaleOrderHeader::class);
        $this->setupAssociations(SaleOrderDetail::class);
    }
}
