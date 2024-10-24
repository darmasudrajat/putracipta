<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Stock\AdjustmentStockMaterialDetail;
use App\Entity\Stock\AdjustmentStockPaperDetail;
use App\Entity\Stock\AdjustmentStockProductDetail;
use App\Entity\Stock\AdjustmentStockHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Stock\AdjustmentStockMaterialDetailRepository;
use App\Repository\Stock\AdjustmentStockPaperDetailRepository;
use App\Repository\Stock\AdjustmentStockProductDetailRepository;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AdjustmentStockHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository;
    private AdjustmentStockMaterialDetailRepository $adjustmentStockMaterialDetailRepository;
    private AdjustmentStockPaperDetailRepository $adjustmentStockPaperDetailRepository;
    private AdjustmentStockProductDetailRepository $adjustmentStockProductDetailRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->adjustmentStockHeaderRepository = $entityManager->getRepository(AdjustmentStockHeader::class);
        $this->adjustmentStockMaterialDetailRepository = $entityManager->getRepository(AdjustmentStockMaterialDetail::class);
        $this->adjustmentStockPaperDetailRepository = $entityManager->getRepository(AdjustmentStockPaperDetail::class);
        $this->adjustmentStockProductDetailRepository = $entityManager->getRepository(AdjustmentStockProductDetail::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if ($options['isFinishedGoods']) {
            $adjustmentStockHeader->setAdjustmentMode(AdjustmentStockHeader::ADJUSTMENT_MODE_PRODUCT);
        }
        if (empty($adjustmentStockHeader->getId())) {
            $adjustmentStockHeader->setCreatedTransactionDateTime($datetime);
            $adjustmentStockHeader->setCreatedTransactionUser($user);
        } else {
            $adjustmentStockHeader->setModifiedTransactionDateTime($datetime);
            $adjustmentStockHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        if ($adjustmentStockHeader->getTransactionDate() !== null && $adjustmentStockHeader->getId() === null) {
            $year = $adjustmentStockHeader->getTransactionDate()->format('y');
            $month = $adjustmentStockHeader->getTransactionDate()->format('m');
            $lastAdjustmentStockHeader = $this->adjustmentStockHeaderRepository->findRecentBy($year, $month);
            $currentAdjustmentStockHeader = ($lastAdjustmentStockHeader === null) ? $adjustmentStockHeader : $lastAdjustmentStockHeader;
            $adjustmentStockHeader->setCodeNumberToNext($currentAdjustmentStockHeader->getCodeNumber(), $year, $month);
        }
        
        if ($adjustmentStockHeader->getWarehouse() !== null) {
            if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_MATERIAL) {
                $materials = array_map(fn($adjustmentStockMaterialDetail) => $adjustmentStockMaterialDetail->getMaterial(), $adjustmentStockHeader->getAdjustmentStockMaterialDetails()->toArray());
                $stockQuantityList = $this->inventoryRepository->getMaterialStockQuantityList($adjustmentStockHeader->getWarehouse(), $materials);
                $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'materialId');
                foreach ($adjustmentStockHeader->getAdjustmentStockMaterialDetails() as $adjustmentStockMaterialDetail) {
                    $adjustmentStockMaterialDetail->setIsCanceled($adjustmentStockMaterialDetail->getSyncIsCanceled());
                    $material = $adjustmentStockMaterialDetail->getMaterial();
                    $stockQuantity = isset($stockQuantityListIndexed[$material->getId()]) ? $stockQuantityListIndexed[$material->getId()] : 0;
                    $adjustmentStockMaterialDetail->setQuantityCurrent($stockQuantity);
                    $adjustmentStockMaterialDetail->setQuantityDifference($adjustmentStockMaterialDetail->getSyncQuantityDifference());
                }
            } else if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_PAPER) {
                $papers = array_map(fn($adjustmentStockPaperDetail) => $adjustmentStockPaperDetail->getPaper(), $adjustmentStockHeader->getAdjustmentStockPaperDetails()->toArray());
                $stockQuantityList = $this->inventoryRepository->getPaperStockQuantityList($adjustmentStockHeader->getWarehouse(), $papers);
                $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'paperId');
                foreach ($adjustmentStockHeader->getAdjustmentStockPaperDetails() as $adjustmentStockPaperDetail) {
                    $adjustmentStockPaperDetail->setIsCanceled($adjustmentStockPaperDetail->getSyncIsCanceled());
                    $paper = $adjustmentStockPaperDetail->getPaper();
                    $stockQuantity = isset($stockQuantityListIndexed[$paper->getId()]) ? $stockQuantityListIndexed[$paper->getId()] : 0;
                    $adjustmentStockPaperDetail->setQuantityCurrent($stockQuantity);
                    $adjustmentStockPaperDetail->setQuantityDifference($adjustmentStockPaperDetail->getSyncQuantityDifference());
                }
            } else if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_PRODUCT) {
                $products = array_map(fn($adjustmentStockProductDetail) => $adjustmentStockProductDetail->getProduct(), $adjustmentStockHeader->getAdjustmentStockProductDetails()->toArray());
                $stockQuantityList = $this->inventoryRepository->getProductStockQuantityList($adjustmentStockHeader->getWarehouse(), $products);
                $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'productId');
                foreach ($adjustmentStockHeader->getAdjustmentStockProductDetails() as $adjustmentStockProductDetail) {
                    $adjustmentStockProductDetail->setIsCanceled($adjustmentStockProductDetail->getSyncIsCanceled());
                    $product = $adjustmentStockProductDetail->getProduct();
                    $stockQuantity = isset($stockQuantityListIndexed[$product->getId()]) ? $stockQuantityListIndexed[$product->getId()] : 0;
                    $adjustmentStockProductDetail->setQuantityCurrent($stockQuantity);
                    $adjustmentStockProductDetail->setQuantityDifference($adjustmentStockProductDetail->getSyncQuantityDifference());
                }
            }
        }
        $adjustmentStockItemList = [];
        $adjustmentStockItemCodeList = [];
        foreach ($adjustmentStockHeader->getAdjustmentStockMaterialDetails() as $adjustmentStockMaterialDetail) {
            if ($adjustmentStockMaterialDetail->isIsCanceled() == false) {
                $material = $adjustmentStockMaterialDetail->getMaterial();
                $adjustmentStockItemList[] = $material->getName();
                $adjustmentStockItemCodeList[] = $material->getCodeNumber();
            }
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockPaperDetails() as $adjustmentStockPaperDetail) {
            if ($adjustmentStockPaperDetail->isIsCanceled() == false) {
                $paper = $adjustmentStockPaperDetail->getPaper();
                $adjustmentStockItemList[] = $paper->getName();
                $adjustmentStockItemCodeList[] = $paper->getCodeNumber();
            }
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockProductDetails() as $adjustmentStockProductDetail) {
            if ($adjustmentStockProductDetail->isIsCanceled() == false) {
                $product = $adjustmentStockProductDetail->getProduct();
                $adjustmentStockItemList[] = $product->getName();
                $adjustmentStockItemCodeList[] = $product->getCode();
            }
        }
        $adjustmentStockItemUniqueList = array_unique(explode(', ', implode(', ', $adjustmentStockItemList)));
        $adjustmentStockHeader->setAdjustmentStockItemList(implode(', ', $adjustmentStockItemUniqueList));
        $adjustmentStockItemCodeUniqueList = array_unique(explode(', ', implode(', ', $adjustmentStockItemCodeList)));
        $adjustmentStockHeader->setAdjustmentStockItemCodeList(implode(', ', $adjustmentStockItemCodeUniqueList));
    }

    public function save(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->adjustmentStockHeaderRepository->add($adjustmentStockHeader);
        foreach ($adjustmentStockHeader->getAdjustmentStockMaterialDetails() as $adjustmentStockMaterialDetail) {
            $this->adjustmentStockMaterialDetailRepository->add($adjustmentStockMaterialDetail);
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockPaperDetails() as $adjustmentStockPaperDetail) {
            $this->adjustmentStockPaperDetailRepository->add($adjustmentStockPaperDetail);
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockProductDetails() as $adjustmentStockProductDetail) {
            $this->adjustmentStockProductDetailRepository->add($adjustmentStockProductDetail);
        }
        $this->addInventories($adjustmentStockHeader);
        $this->entityManager->flush();
    }

    private function addInventories(AdjustmentStockHeader $adjustmentStockHeader): void
    {
        InventoryUtil::reverseOldData($this->inventoryRepository, $adjustmentStockHeader);
        if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_MATERIAL) {
            $adjustmentStockMaterialDetails = $adjustmentStockHeader->getAdjustmentStockMaterialDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('material', $this->purchaseOrderDetailRepository, $adjustmentStockMaterialDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $adjustmentStockHeader, $adjustmentStockMaterialDetails, function($newInventory, $adjustmentStockMaterialDetail) use ($averagePriceList, $adjustmentStockHeader) {
                $material = $adjustmentStockMaterialDetail->getMaterial();
                $purchasePrice = isset($averagePriceList[$material->getId()]) ? $averagePriceList[$material->getId()] : '0.00';
                $newInventory->setTransactionSubject($adjustmentStockMaterialDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setMaterial($material);
                $newInventory->setWarehouse($adjustmentStockHeader->getWarehouse());
                $newInventory->setInventoryMode($adjustmentStockHeader->getAdjustmentMode());
                $newInventory->setQuantityIn($adjustmentStockMaterialDetail->getQuantityDifference());
            });
        } else if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_PAPER) {
            $adjustmentStockPaperDetails = $adjustmentStockHeader->getAdjustmentStockPaperDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('paper', $this->purchaseOrderPaperDetailRepository, $adjustmentStockPaperDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $adjustmentStockHeader, $adjustmentStockPaperDetails, function($newInventory, $adjustmentStockPaperDetail) use ($averagePriceList, $adjustmentStockHeader) {
                $paper = $adjustmentStockPaperDetail->getPaper();
                $purchasePrice = isset($averagePriceList[$paper->getId()]) ? $averagePriceList[$paper->getId()] : '0.00';
                $newInventory->setTransactionSubject($adjustmentStockPaperDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setPaper($paper);
                $newInventory->setWarehouse($adjustmentStockHeader->getWarehouse());
                $newInventory->setInventoryMode($adjustmentStockHeader->getAdjustmentMode());
                $newInventory->setQuantityIn($adjustmentStockPaperDetail->getQuantityDifference());
            });
        } else if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_PRODUCT) {
            $adjustmentStockProductDetails = $adjustmentStockHeader->getAdjustmentStockProductDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('product', $this->saleOrderDetailRepository, $adjustmentStockProductDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $adjustmentStockHeader, $adjustmentStockProductDetails, function($newInventory, $adjustmentStockProductDetail) use ($averagePriceList, $adjustmentStockHeader) {
                $product = $adjustmentStockProductDetail->getProduct();
                $purchasePrice = isset($averagePriceList[$product->getId()]) ? $averagePriceList[$product->getId()] : '0.00';
                $newInventory->setTransactionSubject($adjustmentStockProductDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setProduct($product);
                $newInventory->setWarehouse($adjustmentStockHeader->getWarehouse());
                $newInventory->setInventoryMode($adjustmentStockHeader->getAdjustmentMode());
                $newInventory->setQuantityIn($adjustmentStockProductDetail->getQuantityDifference());
            });
        }
    }
}
