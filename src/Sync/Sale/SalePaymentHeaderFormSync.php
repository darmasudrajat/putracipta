<?php

namespace App\Sync\Sale;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Sale\SalePaymentDetail;
use App\Entity\Sale\SalePaymentHeader;

class SalePaymentHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(SalePaymentHeader::class);
        $this->setupAssociations(SalePaymentDetail::class);
    }
}
