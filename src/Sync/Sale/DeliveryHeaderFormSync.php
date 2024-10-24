<?php

namespace App\Sync\Sale;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Sale\DeliveryDetail;
use App\Entity\Sale\DeliveryHeader;

class DeliveryHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(DeliveryHeader::class);
        $this->setupAssociations(DeliveryDetail::class);
    }
}
