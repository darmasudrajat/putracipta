<?php

namespace App\Sync\Stock;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Stock\AdjustmentStockMaterialDetail;
use App\Entity\Stock\AdjustmentStockPaperDetail;
use App\Entity\Stock\AdjustmentStockProductDetail;
use App\Entity\Stock\AdjustmentStockHeader;

class AdjustmentStockHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(AdjustmentStockHeader::class);
        $this->setupAssociations(AdjustmentStockMaterialDetail::class);
        $this->setupAssociations(AdjustmentStockPaperDetail::class);
        $this->setupAssociations(AdjustmentStockProductDetail::class);
    }
}
