<?php

namespace App\Sync\Production;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Production\MasterOrderCheckSheetDetail;
use App\Entity\Production\MasterOrderDistributionDetail;
use App\Entity\Production\MasterOrderProcessDetail;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Production\MasterOrderPrototypeDetail;
use App\Entity\Production\MasterOrderHeader;

class MasterOrderHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(MasterOrderHeader::class);
        $this->setupAssociations(MasterOrderCheckSheetDetail::class);
        $this->setupAssociations(MasterOrderDistributionDetail::class);
        $this->setupAssociations(MasterOrderProcessDetail::class);
        $this->setupAssociations(MasterOrderProductDetail::class);
        $this->setupAssociations(MasterOrderPrototypeDetail::class);
    }
}
