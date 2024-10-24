<?php

namespace App\Sync\Accounting;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Accounting\JournalVoucherDetail;
use App\Entity\Accounting\JournalVoucherHeader;

class JournalVoucherHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(JournalVoucherHeader::class);
        $this->setupAssociations(JournalVoucherDetail::class);
    }
}
