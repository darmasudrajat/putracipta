<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\PurchasePaymentDetail;
use App\Entity\Purchase\PurchasePaymentHeader;

class PurchasePaymentHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(PurchasePaymentHeader::class);
        $this->setupAssociations(PurchasePaymentDetail::class);
    }
}
