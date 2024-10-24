<?php

namespace App\Sync\Stock;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Stock\StockTransferDetail;
use App\Entity\Stock\StockTransferHeader;

class StockTransferHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(StockTransferHeader::class);
        $this->setupAssociations(StockTransferDetail::class);
    }
}
