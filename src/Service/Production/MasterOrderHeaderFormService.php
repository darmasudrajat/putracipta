<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Warehouse;
use App\Entity\Production\MasterOrderCheckSheetDetail;
use App\Entity\Production\MasterOrderDistributionDetail;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\MasterOrderProcessDetail;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Production\MasterOrderPrototypeDetail;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Master\WarehouseRepository;
use App\Repository\Production\MasterOrderCheckSheetDetailRepository;
use App\Repository\Production\MasterOrderDistributionDetailRepository;
use App\Repository\Production\MasterOrderHeaderRepository;
use App\Repository\Production\MasterOrderProcessDetailRepository;
use App\Repository\Production\MasterOrderProductDetailRepository;
use App\Repository\Production\MasterOrderPrototypeDetailRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Production\MasterOrderHeaderFormSupport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MasterOrderHeaderFormService
{
    use MasterOrderHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private InventoryRepository $inventoryRepository;
    private WarehouseRepository $warehouseRepository;
    private MasterOrderHeaderRepository $masterOrderHeaderRepository;
    private MasterOrderProductDetailRepository $masterOrderProductDetailRepository;
    private MasterOrderProcessDetailRepository $masterOrderProcessDetailRepository;
    private MasterOrderDistributionDetailRepository $masterOrderDistributionDetailRepository;
    private MasterOrderCheckSheetDetailRepository $masterOrderCheckSheetDetailRepository;
    private MasterOrderPrototypeDetailRepository $masterOrderPrototypeDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
        $this->masterOrderHeaderRepository = $entityManager->getRepository(MasterOrderHeader::class);
        $this->masterOrderProductDetailRepository = $entityManager->getRepository(MasterOrderProductDetail::class);
        $this->masterOrderProcessDetailRepository = $entityManager->getRepository(MasterOrderProcessDetail::class);
        $this->masterOrderDistributionDetailRepository = $entityManager->getRepository(MasterOrderDistributionDetail::class);
        $this->masterOrderCheckSheetDetailRepository = $entityManager->getRepository(MasterOrderCheckSheetDetail::class);
        $this->masterOrderPrototypeDetailRepository = $entityManager->getRepository(MasterOrderPrototypeDetail::class);
        $this->warehouseRepository = $entityManager->getRepository(Warehouse::class);
    }

    public function initialize(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($masterOrderHeader->getId())) {
            $masterOrderHeader->setCreatedTransactionDateTime($datetime);
            $masterOrderHeader->setCreatedTransactionUser($user);
        } else {
            $masterOrderHeader->setModifiedTransactionDateTime($datetime);
            $masterOrderHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        if ($masterOrderHeader->getTransactionDate() !== null && $masterOrderHeader->getId() === null) {
            $year = $masterOrderHeader->getTransactionDate()->format('y');
            $month = $masterOrderHeader->getTransactionDate()->format('m');
            $lastMasterOrderHeader = $this->masterOrderHeaderRepository->findRecentBy($year);
            $currentMasterOrderHeader = ($lastMasterOrderHeader === null) ? $masterOrderHeader : $lastMasterOrderHeader;
            $masterOrderHeader->setCodeNumberToNext($currentMasterOrderHeader->getCodeNumber(), $year, $month);
        }
        
        foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
            $saleOrderDetail = $masterOrderProductDetail->getSaleOrderDetail();
            $masterOrderProductDetail->setQuantityShortage($masterOrderProductDetail->getSyncQuantityShortage());
            $masterOrderProductDetail->setRemainingInventoryReceive($masterOrderProductDetail->getSyncRemainingInventoryReceive());
            $masterOrderProductDetail->setRemainingStockDelivery($masterOrderProductDetail->getSyncRemainingStockDelivery());
            if (!empty($saleOrderDetail)) {
                $masterOrderProductDetail->setProduct($saleOrderDetail->getProduct());
                $masterOrderProductDetail->setQuantityOrder($saleOrderDetail->getQuantity());
                $oldMasterOrderProductDetails = $this->masterOrderProductDetailRepository->findBySaleOrderDetail($saleOrderDetail);
                $totalProduction = 0;
                foreach ($oldMasterOrderProductDetails as $oldMasterOrderProductDetail) {
                    if ($oldMasterOrderProductDetail->getId() !== $masterOrderProductDetail->getId() && $oldMasterOrderProductDetail->isIsCanceled() === false) {
                        $totalProduction += $oldMasterOrderProductDetail->getQuantityProduction();
                    }
                }
                if ($masterOrderProductDetail->isIsCanceled() === false) {
                    $totalProduction += $masterOrderProductDetail->getQuantityProduction();
                }
                $saleOrderDetail->setQuantityProduction($totalProduction);
                $saleOrderDetail->setQuantityProductionRemaining($saleOrderDetail->getSyncRemainingProduction());
            }
        }
        
        foreach ($masterOrderHeader->getMasterOrderPrototypeDetails() as $masterOrderPrototypeDetail) {
            $masterOrderPrototypeDetail->setQuantityShortage($masterOrderPrototypeDetail->getSyncQuantityShortage());
        }
        
        $masterOrderHeader->setTotalQuantityOrder($masterOrderHeader->getSyncTotalQuantityOrder());
        $masterOrderHeader->setTotalQuantityStock($masterOrderHeader->getSyncTotalQuantityStock());
        $masterOrderHeader->setTotalQuantityShortage($masterOrderHeader->getSyncTotalQuantityShortage());
        $masterOrderHeader->setTotalQuantityProduction($masterOrderHeader->getSyncTotalQuantityProduction());
        $masterOrderHeader->setTotalRemainingInventoryReceive($masterOrderHeader->getSyncTotalRemainingInventoryReceive());
        
        $product = $masterOrderHeader->getPaper();
        if (!empty($product)) {
            $masterOrderHeader->setPaperPlanoLength($product->getLength());
            $masterOrderHeader->setPaperPlanoWidth($product->getWidth());
            
            $stockQuantityList = $this->inventoryRepository->getAllWarehousePaperStockQuantityList($product);
            $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'paperId');
            $stockQuantity = isset($stockQuantityListIndexed[$product->getId()]) ? $stockQuantityListIndexed[$product->getId()] : 0;
            $masterOrderHeader->setQuantityStockPaper($stockQuantity);
        }
        $masterOrderHeader->setQuantityPaper($masterOrderHeader->getSyncQuantityPaper());
        $masterOrderHeader->setPaperTotal($masterOrderHeader->getSyncPaperTotal());
        $masterOrderHeader->setInsitPrintingQuantity($masterOrderHeader->getSyncInsitPrintingQuantity());
        $masterOrderHeader->setInsitSortingQuantity($masterOrderHeader->getSyncInsitSortingQuantity());
        $masterOrderHeader->setPaperRequirement($masterOrderHeader->getSyncPaperRequirement());
        $masterOrderHeader->setInkCyanWeight($masterOrderHeader->getSyncInkCyanWeight());
        $masterOrderHeader->setInkMagentaWeight($masterOrderHeader->getSyncInkMagentaWeight());
        $masterOrderHeader->setInkYellowWeight($masterOrderHeader->getSyncInkYellowWeight());
        $masterOrderHeader->setInkBlackWeight($masterOrderHeader->getSyncInkBlackWeight());
        $masterOrderHeader->setInkOpvWeight($masterOrderHeader->getSyncInkOpvWeight());
        $masterOrderHeader->setInkK1Weight($masterOrderHeader->getSyncInkK1Weight());
        $masterOrderHeader->setInkK2Weight($masterOrderHeader->getSyncInkK2Weight());
        $masterOrderHeader->setInkK3Weight($masterOrderHeader->getSyncInkK3Weight());
        $masterOrderHeader->setInkK4Weight($masterOrderHeader->getSyncInkK4Weight());
        $masterOrderHeader->setInkLaminatingQuantity($masterOrderHeader->getSyncInkLaminatingQuantity());
        $masterOrderHeader->setPackagingGlueWeight($masterOrderHeader->getSyncPackagingGlueWeight());
        $masterOrderHeader->setPackagingRubberWeight($masterOrderHeader->getSyncPackagingRubberWeight());
        $masterOrderHeader->setPackagingPaperWeight($masterOrderHeader->getSyncPackagingPaperWeight());
        $masterOrderHeader->setPackagingBoxWeight($masterOrderHeader->getSyncPackagingBoxWeight());
        $masterOrderHeader->setPackagingTapeLargeSize($masterOrderHeader->getSyncPackagingTapeLargeSize());
        $masterOrderHeader->setPackagingTapeSmallSize($masterOrderHeader->getSyncPackagingTapeSmallSize());
        $masterOrderHeader->setPackagingPlasticSize($masterOrderHeader->getSyncPackagingPlasticSize());
        
        if ($options['transactionFile']) {
            $masterOrderHeader->setLayoutModelFileExtension($options['transactionFile']->guessExtension());
        }
        
        $saleOrderReferenceNumberList = [];
        foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
            $saleOrderDetail = $masterOrderProductDetail->getSaleOrderDetail();
            if (!empty($saleOrderDetail)) {
                $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();
                $saleOrderReferenceNumberList[] = $saleOrderHeader->getReferenceNumber();
            }
        }
        $saleOrderReferenceNumberUniqueList = array_unique(explode(', ', implode(', ', $saleOrderReferenceNumberList)));
        $masterOrderHeader->setSaleOrderReferenceNumberList(implode(', ', $saleOrderReferenceNumberUniqueList));
        
        $masterOrderProductList = [];
        $masterOrderProductNameList = [];
        foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
            if ($masterOrderProductDetail->isIsCanceled() == false) {
                $product = $masterOrderProductDetail->getProduct();
                $masterOrderProductList[] = $product->getCode();
                $masterOrderProductNameList[] = $product->getName();
            }
        }
        $masterOrderProductUniqueList = array_unique(explode(', ', implode(', ', $masterOrderProductList)));
        $masterOrderHeader->setMasterOrderProductList(implode(', ', $masterOrderProductUniqueList));
        $masterOrderProductNameUniqueList = array_unique(explode(', ', implode(', ', $masterOrderProductNameList)));
        $masterOrderHeader->setMasterOrderProductNameList(implode(', ', $masterOrderProductNameUniqueList));
    }

    public function save(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($masterOrderHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->masterOrderHeaderRepository->add($masterOrderHeader);
            foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
                $this->masterOrderProductDetailRepository->add($masterOrderProductDetail);
            }
            foreach ($masterOrderHeader->getMasterOrderProcessDetails() as $masterOrderProcessDetail) {
                $this->masterOrderProcessDetailRepository->add($masterOrderProcessDetail);
            }
            foreach ($masterOrderHeader->getMasterOrderDistributionDetails() as $masterOrderDistributionDetail) {
                $this->masterOrderDistributionDetailRepository->add($masterOrderDistributionDetail);
            }
            foreach ($masterOrderHeader->getMasterOrderCheckSheetDetails() as $masterOrderCheckSheetDetail) {
                $this->masterOrderCheckSheetDetailRepository->add($masterOrderCheckSheetDetail);
            }
            foreach ($masterOrderHeader->getMasterOrderPrototypeDetails() as $masterOrderPrototypeDetail) {
                $this->masterOrderPrototypeDetailRepository->add($masterOrderPrototypeDetail);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($masterOrderHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }

    public function uploadFile(MasterOrderHeader $masterOrderHeader, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $masterOrderHeader->getFileName();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }

    public function copyFrom(MasterOrderHeader $sourceMasterOrderHeader): MasterOrderHeader
    {
        $masterOrderHeader = new MasterOrderHeader();
        $masterOrderHeader->setCustomer($sourceMasterOrderHeader->getCustomer());
        $masterOrderHeader->setDesignCode($sourceMasterOrderHeader->getDesignCode());
        $masterOrderHeader->setOrderType($sourceMasterOrderHeader->getOrderType());
        $masterOrderHeader->setPrintingStatus($sourceMasterOrderHeader->getPrintingStatus());
        $masterOrderHeader->setMachinePrinting($sourceMasterOrderHeader->getMachinePrinting());
        $masterOrderHeader->setPaper($sourceMasterOrderHeader->getPaper());
        $masterOrderHeader->setMountageSize($sourceMasterOrderHeader->getMountageSize());
        $masterOrderHeader->setHotStamping($sourceMasterOrderHeader->getHotStamping());
        $masterOrderHeader->setGlossiness($sourceMasterOrderHeader->getGlossiness());
        $masterOrderHeader->setFinishing($sourceMasterOrderHeader->getFinishing());
        $masterOrderHeader->setColor($sourceMasterOrderHeader->getColor());
        $masterOrderHeader->setPantone($sourceMasterOrderHeader->getPantone());
        $masterOrderHeader->setQuantityPrinting($sourceMasterOrderHeader->getQuantityPrinting());
        $masterOrderHeader->setQuantityPrinting2($sourceMasterOrderHeader->getQuantityPrinting2());
        $masterOrderHeader->setDieCutBlade($sourceMasterOrderHeader->getDieCutBlade());
        $masterOrderHeader->setDiecutKnife($sourceMasterOrderHeader->getDiecutKnife());
        $masterOrderHeader->setDielineMillar($sourceMasterOrderHeader->getDielineMillar());
        $masterOrderHeader->setInsitPrintingPercentage($sourceMasterOrderHeader->getInsitPrintingPercentage());
        $masterOrderHeader->setInsitSortingPercentage($sourceMasterOrderHeader->getInsitSortingPercentage());
        $masterOrderHeader->setPaperMountage($sourceMasterOrderHeader->getPaperMountage());
        $masterOrderHeader->setPaperPlanoLength($sourceMasterOrderHeader->getPaperPlanoLength());
        $masterOrderHeader->setPaperPlanoWidth($sourceMasterOrderHeader->getPaperPlanoWidth());
        $masterOrderHeader->setInkBlackPercentage($sourceMasterOrderHeader->getInkBlackPercentage());
        $masterOrderHeader->setInkCyanPercentage($sourceMasterOrderHeader->getInkCyanPercentage());
        $masterOrderHeader->setInkMagentaPercentage($sourceMasterOrderHeader->getInkMagentaPercentage());
        $masterOrderHeader->setInkYellowPercentage($sourceMasterOrderHeader->getInkYellowPercentage());
        $masterOrderHeader->setInkOpvPercentage($sourceMasterOrderHeader->getInkOpvPercentage());
        $masterOrderHeader->setInkHotStampingSize($sourceMasterOrderHeader->getInkHotStampingSize());
        $masterOrderHeader->setInkK1Percentage($sourceMasterOrderHeader->getInkK1Percentage());
        $masterOrderHeader->setInkK2Percentage($sourceMasterOrderHeader->getInkK2Percentage());
        $masterOrderHeader->setInkK3Percentage($sourceMasterOrderHeader->getInkK3Percentage());
        $masterOrderHeader->setInkK4Percentage($sourceMasterOrderHeader->getInkK4Percentage());
        $masterOrderHeader->setInkK1Color($sourceMasterOrderHeader->getInkK1Color());
        $masterOrderHeader->setInkK2Color($sourceMasterOrderHeader->getInkK2Color());
        $masterOrderHeader->setInkK3Color($sourceMasterOrderHeader->getInkK3Color());
        $masterOrderHeader->setInkK4Color($sourceMasterOrderHeader->getInkK4Color());
        $masterOrderHeader->setInkLaminatingSize($sourceMasterOrderHeader->getInkLaminatingSize());
        $masterOrderHeader->setPackagingBoxQuantity($sourceMasterOrderHeader->getPackagingBoxQuantity());
        $masterOrderHeader->setPackagingGlueQuantity($sourceMasterOrderHeader->getPackagingGlueQuantity());
        $masterOrderHeader->setPackagingPaperQuantity($sourceMasterOrderHeader->getPackagingPaperQuantity());
        $masterOrderHeader->setPackagingPlasticQuantity($sourceMasterOrderHeader->getPackagingPlasticQuantity());
        $masterOrderHeader->setPackagingRubberQuantity($sourceMasterOrderHeader->getPackagingRubberQuantity());
        $masterOrderHeader->setPackagingTapeLargeQuantity($sourceMasterOrderHeader->getPackagingTapeLargeQuantity());
        $masterOrderHeader->setPackagingTapeSmallQuantity($sourceMasterOrderHeader->getPackagingTapeSmallQuantity());
        $masterOrderHeader->setLayoutModelFileExtension($sourceMasterOrderHeader->getLayoutModelFileExtension());
        foreach ($sourceMasterOrderHeader->getMasterOrderProcessDetails() as $sourceMasterOrderProcessDetail) {
            $masterOrderProcessDetail = new MasterOrderProcessDetail();
            $masterOrderProcessDetail->setDesignCodeProcessDetail($sourceMasterOrderProcessDetail->getDesignCodeProcessDetail());
            $masterOrderProcessDetail->setWorkOrderProcess($sourceMasterOrderProcessDetail->getWorkOrderProcess());
            $masterOrderHeader->addMasterOrderProcessDetail($masterOrderProcessDetail);
        }
        foreach ($sourceMasterOrderHeader->getMasterOrderDistributionDetails() as $sourceMasterOrderDistributionDetail) {
            $masterOrderDistributionDetail = new MasterOrderDistributionDetail();
            $masterOrderDistributionDetail->setDesignCodeDistributionDetail($sourceMasterOrderDistributionDetail->getDesignCodeDistributionDetail());
            $masterOrderDistributionDetail->setWorkOrderDistribution($sourceMasterOrderDistributionDetail->getWorkOrderDistribution());
            $masterOrderHeader->addMasterOrderDistributionDetail($masterOrderDistributionDetail);
        }
        foreach ($sourceMasterOrderHeader->getMasterOrderCheckSheetDetails() as $sourceMasterOrderCheckSheetDetail) {
            $masterOrderCheckSheetDetail = new MasterOrderCheckSheetDetail();
            $masterOrderCheckSheetDetail->setDesignCodeCheckSheetDetail($sourceMasterOrderCheckSheetDetail->getDesignCodeCheckSheetDetail());
            $masterOrderCheckSheetDetail->setWorkOrderCheckSheet($sourceMasterOrderCheckSheetDetail->getWorkOrderCheckSheet());
            $masterOrderHeader->addMasterOrderCheckSheetDetail($masterOrderCheckSheetDetail);
        }
        return $masterOrderHeader;
    }
}
