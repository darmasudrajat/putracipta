<?php

namespace App\Sync\Sale;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Sale\SaleInvoiceDetail;
use App\Entity\Sale\SaleInvoiceHeader;

class SaleInvoiceHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(SaleInvoiceHeader::class);
        $this->setupAssociations(SaleInvoiceDetail::class);
    }
}
