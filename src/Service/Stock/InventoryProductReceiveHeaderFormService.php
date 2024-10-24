<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Stock\InventoryProductReceiveDetail;
use App\Entity\Stock\InventoryProductReceiveHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Stock\InventoryProductReceiveDetailRepository;
use App\Repository\Stock\InventoryProductReceiveHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class InventoryProductReceiveHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private InventoryProductReceiveHeaderRepository $inventoryProductReceiveHeaderRepository;
    private InventoryProductReceiveDetailRepository $inventoryProductReceiveDetailRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->inventoryProductReceiveHeaderRepository = $entityManager->getRepository(InventoryProductReceiveHeader::class);
        $this->inventoryProductReceiveDetailRepository = $entityManager->getRepository(InventoryProductReceiveDetail::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(InventoryProductReceiveHeader $inventoryProductReceiveHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($inventoryProductReceiveHeader->getId())) {
            $inventoryProductReceiveHeader->setCreatedTransactionDateTime($datetime);
            $inventoryProductReceiveHeader->setCreatedTransactionUser($user);
        } else {
            $inventoryProductReceiveHeader->setModifiedTransactionDateTime($datetime);
            $inventoryProductReceiveHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(InventoryProductReceiveHeader $inventoryProductReceiveHeader, array $options = []): void
    {
        if ($inventoryProductReceiveHeader->getTransactionDate() !== null && $inventoryProductReceiveHeader->getId() === null) {
            $year = $inventoryProductReceiveHeader->getTransactionDate()->format('y');
            $month = $inventoryProductReceiveHeader->getTransactionDate()->format('m');
            $lastInventoryProductReceiveHeader = $this->inventoryProductReceiveHeaderRepository->findRecentBy($year, $month);
            $currentInventoryProductReceiveHeader = ($lastInventoryProductReceiveHeader === null) ? $inventoryProductReceiveHeader : $lastInventoryProductReceiveHeader;
            $inventoryProductReceiveHeader->setCodeNumberToNext($currentInventoryProductReceiveHeader->getCodeNumber(), $year, $month);
        }
        
        foreach ($inventoryProductReceiveHeader->getInventoryProductReceiveDetails() as $inventoryProductReceiveDetail) {
            $inventoryProductReceiveDetail->setIsCanceled($inventoryProductReceiveDetail->getSyncIsCanceled());
            $inventoryProductReceiveDetail->setQuantityTotalPieces($inventoryProductReceiveDetail->getSyncQuantityTotalPieces());
            
            $masterOrderProductDetail = $inventoryProductReceiveDetail->getMasterOrderProductDetail();
            $inventoryProductReceiveDetail->setSaleOrderDetail($masterOrderProductDetail->getSaleOrderDetail());
            
            $oldInventoryProductReceiveDetails = $this->inventoryProductReceiveDetailRepository->findByMasterOrderProductDetail($masterOrderProductDetail);
            $totalProduction = 0;
            foreach ($oldInventoryProductReceiveDetails as $oldInventoryProductReceiveDetail) {
                if ($oldInventoryProductReceiveDetail->getId() !== $inventoryProductReceiveDetail->getId() && $oldInventoryProductReceiveDetail->isIsCanceled() === false) {
                    $totalProduction += $oldInventoryProductReceiveDetail->getQuantityTotalPieces();
                }
            }
            if ($inventoryProductReceiveDetail->isIsCanceled() === false) {
                $totalProduction += $inventoryProductReceiveDetail->getQuantityTotalPieces();
            }
            $masterOrderProductDetail->setQuantityInventoryReceive($totalProduction);
            $masterOrderProductDetail->setRemainingInventoryReceive($masterOrderProductDetail->getSyncRemainingInventoryReceive());
            $masterOrderProductDetail->setRemainingStockDelivery($masterOrderProductDetail->getSyncRemainingStockDelivery());
            
        }
        $inventoryProductReceiveHeader->setTotalQuantity($inventoryProductReceiveHeader->getSyncTotalQuantity());
        $masterOrderHeader = $inventoryProductReceiveHeader->getMasterOrderHeader();
        if ($masterOrderHeader !== null) {
            $masterOrderHeader->setTotalRemainingInventoryReceive($masterOrderHeader->getSyncTotalRemainingInventoryReceive());
        }
        
        $productNameList = array();
        $productCodeList = array();
        foreach ($inventoryProductReceiveHeader->getInventoryProductReceiveDetails() as $inventoryProductReceiveDetail) {
            $product = $inventoryProductReceiveDetail->getProduct();
            $productNameList[] = $product->getName();
            $productCodeList[] = $product->getCode();
        }
        $productNameUniqueList = array_unique(explode(', ', implode(', ', $productNameList)));
        $inventoryProductReceiveHeader->setProductDetailLists(implode(', ', $productNameUniqueList));
        $productCodeUniqueList = array_unique(explode(', ', implode(', ', $productCodeList)));
        $inventoryProductReceiveHeader->setProductCodeLists(implode(', ', $productCodeUniqueList));
    }

    public function save(InventoryProductReceiveHeader $inventoryProductReceiveHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->inventoryProductReceiveHeaderRepository->add($inventoryProductReceiveHeader);
        foreach ($inventoryProductReceiveHeader->getInventoryProductReceiveDetails() as $inventoryProductReceiveDetail) {
            $this->inventoryProductReceiveDetailRepository->add($inventoryProductReceiveDetail);
        }
        $this->addInventories($inventoryProductReceiveHeader);
        $this->entityManager->flush();
    }

    private function addInventories(InventoryProductReceiveHeader $inventoryProductReceiveHeader): void {
        InventoryUtil::reverseOldData($this->inventoryRepository, $inventoryProductReceiveHeader);
        $inventoryProductReceiveDetails = $inventoryProductReceiveHeader->getInventoryProductReceiveDetails()->toArray();
        $averagePriceList = InventoryUtil::getAveragePriceList('product', $this->saleOrderDetailRepository, $inventoryProductReceiveDetails);
        InventoryUtil::addNewData($this->inventoryRepository, $inventoryProductReceiveHeader, $inventoryProductReceiveDetails, function ($newInventory, $inventoryProductReceiveDetail) use ($averagePriceList, $inventoryProductReceiveHeader) {
            $product = $inventoryProductReceiveDetail->getProduct();
            $purchasePrice = isset($averagePriceList[$product->getId()]) ? $averagePriceList[$product->getId()] : '0.00';
            $newInventory->setTransactionSubject($inventoryProductReceiveHeader->getMasterOrderHeader()->getCustomer()->getCompany());
            $newInventory->setPurchasePrice($purchasePrice);
            $newInventory->setProduct($product);
            $newInventory->setWarehouse($inventoryProductReceiveHeader->getWarehouse());
            $newInventory->setInventoryMode('product');
            $newInventory->setQuantityIn($inventoryProductReceiveDetail->getQuantityTotalPieces());
        });
    }
}
