<?php

namespace App\Tests\Service\Transaction;

use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Service\Transaction\PurchaseOrderHeaderFormService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurchaseOrderHeaderFormServiceTest extends KernelTestCase
{
    private PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->purchaseOrderHeaderFormService = self::$container->get(PurchaseOrderHeaderFormService::class);
    }

    public function testSync(): void
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $purchaseOrderDetail = new PurchaseOrderDetail();
        $purchaseOrderDetail->setQuantity(2);
        $purchaseOrderDetail->setUnitPrice('2000.00');
        $purchaseOrderHeader->addPurchaseOrderDetail($purchaseOrderDetail);
        $purchaseOrderDetail = new PurchaseOrderDetail();
        $purchaseOrderDetail->setQuantity(3);
        $purchaseOrderDetail->setUnitPrice('3000.00');
        $purchaseOrderHeader->addPurchaseOrderDetail($purchaseOrderDetail);
        $purchaseOrderDetails = $purchaseOrderHeader->getPurchaseOrderDetails();
        $this->assertEquals($purchaseOrderDetails[0]->getTotal(), '4000.00');
        $this->assertEquals($purchaseOrderDetails[1]->getTotal(), '9000.00');
    }
}
