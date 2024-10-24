<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Stock\Inventory;
use App\Entity\Stock\InventoryReleaseMaterialDetail;
use App\Entity\Stock\InventoryReleasePaperDetail;
use App\Entity\Stock\InventoryReleaseHeader;
use App\Entity\Stock\InventoryRequestHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Stock\InventoryReleaseMaterialDetailRepository;
use App\Repository\Stock\InventoryReleasePaperDetailRepository;
use App\Repository\Stock\InventoryReleaseHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class InventoryReleaseHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private InventoryReleaseHeaderRepository $inventoryReleaseHeaderRepository;
    private InventoryReleaseMaterialDetailRepository $inventoryReleaseMaterialDetailRepository;
    private InventoryReleasePaperDetailRepository $inventoryReleasePaperDetailRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->inventoryReleaseHeaderRepository = $entityManager->getRepository(InventoryReleaseHeader::class);
        $this->inventoryReleaseMaterialDetailRepository = $entityManager->getRepository(InventoryReleaseMaterialDetail::class);
        $this->inventoryReleasePaperDetailRepository = $entityManager->getRepository(InventoryReleasePaperDetail::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(InventoryReleaseHeader $inventoryReleaseHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($inventoryReleaseHeader->getId())) {
            $inventoryReleaseHeader->setCreatedTransactionDateTime($datetime);
            $inventoryReleaseHeader->setCreatedTransactionUser($user);
        } else {
            $inventoryReleaseHeader->setModifiedTransactionDateTime($datetime);
            $inventoryReleaseHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(InventoryReleaseHeader $inventoryReleaseHeader, array $options = []): void
    {
        if ($inventoryReleaseHeader->getTransactionDate() !== null && $inventoryReleaseHeader->getId() === null) {
            $year = $inventoryReleaseHeader->getTransactionDate()->format('y');
            $month = $inventoryReleaseHeader->getTransactionDate()->format('m');
            $lastInventoryReleaseHeader = $this->inventoryReleaseHeaderRepository->findRecentBy($year, $month);
            $currentInventoryReleaseHeader = ($lastInventoryReleaseHeader === null) ? $inventoryReleaseHeader : $lastInventoryReleaseHeader;
            $inventoryReleaseHeader->setCodeNumberToNext($currentInventoryReleaseHeader->getCodeNumber(), $year, $month);
        }
        
        if ($inventoryReleaseHeader->getWarehouse() !== null) {
            if ($inventoryReleaseHeader->getReleaseMode() === InventoryReleaseHeader::RELEASE_MODE_MATERIAL) {
                $materials = array_map(fn($inventoryReleaseMaterialDetail) => $inventoryReleaseMaterialDetail->getMaterial(), $inventoryReleaseHeader->getInventoryReleaseMaterialDetails()->toArray());
                $stockQuantityList = $this->inventoryRepository->getMaterialStockQuantityList($inventoryReleaseHeader->getWarehouse(), $materials);
                $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'materialId');
                foreach ($inventoryReleaseHeader->getInventoryReleaseMaterialDetails() as $inventoryReleaseMaterialDetail) {
                    $inventoryRequestMaterialDetail = $inventoryReleaseMaterialDetail->getInventoryRequestMaterialDetail();
                    $inventoryReleaseMaterialDetail->setIsCanceled($inventoryReleaseMaterialDetail->getSyncIsCanceled());
//                    $inventoryReleaseMaterialDetail->setUnit($inventoryRequestMaterialDetail->getUnit());
                    $material = $inventoryReleaseMaterialDetail->getMaterial();
                    $stockQuantity = isset($stockQuantityListIndexed[$material->getId()]) ? $stockQuantityListIndexed[$material->getId()] : 0;
                    $inventoryReleaseMaterialDetail->setQuantityCurrent($stockQuantity);

                    $oldReleaseDetails = $this->inventoryReleaseMaterialDetailRepository->findByInventoryRequestMaterialDetail($inventoryRequestMaterialDetail);
                    $totalRelease = 0;
                    foreach ($oldReleaseDetails as $oldReleaseDetail) {
                        if ($oldReleaseDetail->getId() !== $inventoryReleaseMaterialDetail->getId() && $oldReleaseDetail->isIsCanceled() === false) {
                            $totalRelease += $oldReleaseDetail->getQuantity();
                        }
                    }
                    if ($inventoryReleaseMaterialDetail->isIsCanceled() === false) {
                        $totalRelease += $inventoryReleaseMaterialDetail->getQuantity();
                    }
                    
                    if (!empty($inventoryRequestMaterialDetail)) {
                        $inventoryRequestHeader = $inventoryRequestMaterialDetail->getInventoryRequestHeader();
                        $inventoryRequestMaterialDetail->setQuantityRelease($totalRelease);
                        $inventoryRequestMaterialDetail->setQuantityRemaining($inventoryRequestMaterialDetail->getSyncQuantityRemaining());
                        $inventoryRequestHeader->setTotalQuantityRelease($inventoryRequestHeader->getSyncTotalQuantityRelease());
                        $inventoryRequestHeader->setTotalQuantityRemaining($inventoryRequestHeader->getSyncTotalQuantityRemaining());
                        $requestStatus = $inventoryRequestHeader->getTotalQuantityRemaining() > '0.00' ? InventoryRequestHeader::REQUEST_STATUS_PARTIAL : InventoryRequestHeader::REQUEST_STATUS_CLOSE;
                        $inventoryRequestHeader->setRequestStatus($requestStatus);
                    }
                }
            } elseif ($inventoryReleaseHeader->getReleaseMode() === InventoryReleaseHeader::RELEASE_MODE_PAPER) {
                $papers = array_map(fn($inventoryReleasePaperDetail) => $inventoryReleasePaperDetail->getPaper(), $inventoryReleaseHeader->getInventoryReleasePaperDetails()->toArray());
                $stockQuantityList = $this->inventoryRepository->getPaperStockQuantityList($inventoryReleaseHeader->getWarehouse(), $papers);
                $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'paperId');
                foreach ($inventoryReleaseHeader->getInventoryReleasePaperDetails() as $inventoryReleasePaperDetail) {
                    $inventoryRequestPaperDetail = $inventoryReleasePaperDetail->getInventoryRequestPaperDetail();
                    $inventoryReleasePaperDetail->setIsCanceled($inventoryReleasePaperDetail->getSyncIsCanceled());
                    $paper = $inventoryReleasePaperDetail->getPaper();
                    $stockQuantity = isset($stockQuantityListIndexed[$paper->getId()]) ? $stockQuantityListIndexed[$paper->getId()] : 0;
                    $inventoryReleasePaperDetail->setQuantityCurrent($stockQuantity);

                    $oldReleaseDetails = $this->inventoryReleasePaperDetailRepository->findByInventoryRequestPaperDetail($inventoryRequestPaperDetail);
                    $totalRelease = 0;
                    foreach ($oldReleaseDetails as $oldReleaseDetail) {
                        if ($oldReleaseDetail->getId() !== $inventoryReleasePaperDetail->getId() && $oldReleaseDetail->isIsCanceled() === false) {
                            $totalRelease += $oldReleaseDetail->getQuantity();
                        }
                    }
                    if ($inventoryReleasePaperDetail->isIsCanceled() === false) {
                        $totalRelease += $inventoryReleasePaperDetail->getQuantity();
                    }
                    
                    if (!empty($inventoryRequestPaperDetail)) {
                        $inventoryRequestHeader = $inventoryRequestPaperDetail->getInventoryRequestHeader();
                        $inventoryRequestPaperDetail->setQuantityRelease($totalRelease);
                        $inventoryRequestPaperDetail->setQuantityRemaining($inventoryRequestPaperDetail->getSyncQuantityRemaining());
                        $inventoryRequestHeader->setTotalQuantityRelease($inventoryRequestHeader->getSyncTotalQuantityRelease());
                        $inventoryRequestHeader->setTotalQuantityRemaining($inventoryRequestHeader->getSyncTotalQuantityRemaining());
                        $requestStatus = $inventoryRequestHeader->getTotalQuantityRemaining() > '0.00' ? InventoryRequestHeader::REQUEST_STATUS_PARTIAL : InventoryRequestHeader::REQUEST_STATUS_CLOSE;
                        $inventoryRequestHeader->setRequestStatus($requestStatus);
                    }
                }
            }
        }
        $inventoryReleaseHeader->setTotalQuantity($inventoryReleaseHeader->getSyncTotalQuantity());
        
        $inventoryReleaseItemList = [];
        foreach ($inventoryReleaseHeader->getInventoryReleaseMaterialDetails() as $inventoryReleaseMaterialDetail) {
            $material = $inventoryReleaseMaterialDetail->getMaterial();
            $inventoryReleaseItemList[] = $material->getName();
        }
        foreach ($inventoryReleaseHeader->getInventoryReleasePaperDetails() as $inventoryReleasePaperDetail) {
            $paper = $inventoryReleasePaperDetail->getPaper();
            $inventoryReleaseItemList[] = $paper->getName();
        }
        $inventoryReleaseItemUniqueList = array_unique(explode(', ', implode(', ', $inventoryReleaseItemList)));
        $inventoryReleaseHeader->setInventoryReleaseItemList(implode(', ', $inventoryReleaseItemUniqueList));
    }

    public function save(InventoryReleaseHeader $inventoryReleaseHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->inventoryReleaseHeaderRepository->add($inventoryReleaseHeader);
        foreach ($inventoryReleaseHeader->getInventoryReleaseMaterialDetails() as $inventoryReleaseMaterialDetail) {
            $this->inventoryReleaseMaterialDetailRepository->add($inventoryReleaseMaterialDetail);
        }
        foreach ($inventoryReleaseHeader->getInventoryReleasePaperDetails() as $inventoryReleasePaperDetail) {
            $this->inventoryReleasePaperDetailRepository->add($inventoryReleasePaperDetail);
        }
        $this->addInventories($inventoryReleaseHeader);
        $this->entityManager->flush();
    }

    private function addInventories(InventoryReleaseHeader $inventoryReleaseHeader): void
    {
        InventoryUtil::reverseOldData($this->inventoryRepository, $inventoryReleaseHeader);
        if ($inventoryReleaseHeader->getReleaseMode() === InventoryReleaseHeader::RELEASE_MODE_MATERIAL) {
            $inventoryReleaseMaterialDetails = $inventoryReleaseHeader->getInventoryReleaseMaterialDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('material', $this->purchaseOrderDetailRepository, $inventoryReleaseMaterialDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $inventoryReleaseHeader, $inventoryReleaseMaterialDetails, function($newInventory, $inventoryReleaseMaterialDetail) use ($averagePriceList, $inventoryReleaseHeader) {
                $material = $inventoryReleaseMaterialDetail->getMaterial();
                $purchasePrice = isset($averagePriceList[$material->getId()]) ? $averagePriceList[$material->getId()] : '0.00';
                $newInventory->setTransactionSubject($inventoryReleaseMaterialDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setMaterial($material);
                $newInventory->setWarehouse($inventoryReleaseHeader->getWarehouse());
                $newInventory->setInventoryMode($inventoryReleaseHeader->getReleaseMode());
                $newInventory->setQuantityOut($inventoryReleaseMaterialDetail->getQuantity());
            });
        } else if ($inventoryReleaseHeader->getReleaseMode() === InventoryReleaseHeader::RELEASE_MODE_PAPER) {
            $inventoryReleasePaperDetails = $inventoryReleaseHeader->getInventoryReleasePaperDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('paper', $this->purchaseOrderPaperDetailRepository, $inventoryReleasePaperDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $inventoryReleaseHeader, $inventoryReleasePaperDetails, function($newInventory, $inventoryReleasePaperDetail) use ($averagePriceList, $inventoryReleaseHeader) {
                $paper = $inventoryReleasePaperDetail->getPaper();
                $purchasePrice = isset($averagePriceList[$paper->getId()]) ? $averagePriceList[$paper->getId()] : '0.00';
                $newInventory->setTransactionSubject($inventoryReleasePaperDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setPaper($paper);
                $newInventory->setWarehouse($inventoryReleaseHeader->getWarehouse());
                $newInventory->setInventoryMode($inventoryReleaseHeader->getReleaseMode());
                $newInventory->setQuantityOut($inventoryReleasePaperDetail->getQuantity());
            });
        }
    }
}
