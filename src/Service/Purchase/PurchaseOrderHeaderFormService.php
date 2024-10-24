<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Entity\Purchase\PurchaseRequestDetail;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Purchase\PurchaseOrderHeaderFormSupport;
use App\Sync\Purchase\PurchaseOrderHeaderFormSync;
use App\Util\Service\EntityResetUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseOrderHeaderFormService
{
    use PurchaseOrderHeaderFormSupport;

    private PurchaseOrderHeaderFormSync $formSync;
    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;

    public function __construct(RequestStack $requestStack, PurchaseOrderHeaderFormSync $formSync, EntityManagerInterface $entityManager)
    {
        $this->formSync = $formSync;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->purchaseOrderHeaderRepository = $entityManager->getRepository(PurchaseOrderHeader::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
    }

    public function initialize(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (isset($options['cancelTransaction']) && $options['cancelTransaction'] === true) {
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_CANCEL);
            $purchaseOrderHeader->setIsCanceled(true);
            $purchaseOrderHeader->setCancelledTransactionDateTime($datetime);
            $purchaseOrderHeader->setCancelledTransactionUser($user);
        } else {
            if (empty($purchaseOrderHeader->getId())) {
                $purchaseOrderHeader->setCreatedTransactionDateTime($datetime);
                $purchaseOrderHeader->setCreatedTransactionUser($user);
            } else {
                $purchaseOrderHeader->setModifiedTransactionDateTime($datetime);
                $purchaseOrderHeader->setModifiedTransactionUser($user);
            }
            
            $purchaseOrderHeader->setCodeNumberVersion($purchaseOrderHeader->getCodeNumberVersion() + 1);
        }
    }

    public function finalize(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        if (isset($options['cancelTransaction']) && $options['cancelTransaction'] === true) {
            EntityResetUtil::reset($this->formSync, $purchaseOrderHeader);
        } else {
            foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
                EntityResetUtil::reset($this->formSync, $purchaseOrderDetail);
            }
        }
        
        if ($purchaseOrderHeader->getTransactionDate() !== null && $purchaseOrderHeader->getId() === null) {
            $year = $purchaseOrderHeader->getTransactionDate()->format('y');
            $month = $purchaseOrderHeader->getTransactionDate()->format('m');
            $lastPurchaseOrderHeader = $this->purchaseOrderHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseOrderHeader = ($lastPurchaseOrderHeader === null) ? $purchaseOrderHeader : $lastPurchaseOrderHeader;
            $purchaseOrderHeader->setCodeNumberToNext($currentPurchaseOrderHeader->getCodeNumber(), $year, $month);

        }
        
        if ($purchaseOrderHeader->getTaxMode() !== $purchaseOrderHeader::TAX_MODE_NON_TAX) {
            $purchaseOrderHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $purchaseOrderHeader->setTaxPercentage(0);
        }
        
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setIsCanceled($purchaseOrderDetail->getSyncIsCanceled());
            $purchaseOrderDetail->setIsTransactionClosed($purchaseOrderDetail->getSyncIsTransactionClosed());
            if ($purchaseOrderDetail->isIsCanceled()) {
                $purchaseOrderDetail->setPurchaseRequestDetail(null);
            }
            
            $purchaseOrderDetail->setRemainingReceive($purchaseOrderDetail->getSyncRemainingReceive());
            $purchaseOrderDetail->setUnitPriceBeforeTax($purchaseOrderDetail->getSyncUnitPriceBeforeTax());
            $purchaseOrderDetail->setTotal($purchaseOrderDetail->getSyncTotal());
            
            if ($purchaseOrderDetail->getRemainingReceive() === 0) {
                $purchaseOrderDetail->setIsTransactionClosed(true);
            }
            
            if ($purchaseOrderDetail->isIsTransactionClosed() === true or $purchaseOrderDetail->isIsCanceled() === true) {
                $purchaseOrderDetail->setRemainingReceive(0);
            }
            
            $purchaseRequestDetail = $purchaseOrderDetail->getPurchaseRequestDetail();
            if ($purchaseRequestDetail !== null && $purchaseOrderHeader->getId() === null) {
                $purchaseRequestDetail->setTransactionStatus(PurchaseRequestDetail::TRANSACTION_STATUS_PURCHASE);
            }
            
        }
        $supplier = $purchaseOrderHeader->getSupplier();
        $purchaseOrderHeader->setCurrency($supplier === null ? null : $supplier->getCurrency());
        $purchaseOrderHeader->setSubTotal($purchaseOrderHeader->getSyncSubTotal());
        $purchaseOrderHeader->setTaxNominal($purchaseOrderHeader->getSyncTaxNominal());
        $purchaseOrderHeader->setGrandTotal($purchaseOrderHeader->getSyncGrandTotal());
        $purchaseOrderHeader->setTotalRemainingReceive($purchaseOrderHeader->getSyncTotalRemainingReceive());
        
        $purchaseOrderMaterialList = [];
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            if ($purchaseOrderDetail->isIsCanceled() == false) {
                $material = $purchaseOrderDetail->getMaterial();
                $purchaseOrderMaterialList[] = $material->getName();
            }
        }
        $purchaseOrderMaterialUniqueList = array_unique(explode(', ', implode(', ', $purchaseOrderMaterialList)));
        $purchaseOrderHeader->setPurchaseOrderMaterialList(implode(', ', $purchaseOrderMaterialUniqueList));
    }

    public function save(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($purchaseOrderHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader);
            foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
                $this->purchaseOrderDetailRepository->add($purchaseOrderDetail);
            }
            $entityManager->flush();
            $transactionLog = $this->buildTransactionLog($purchaseOrderHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }

    public function createSyncView(): array
    {
        return $this->formSync->getView();
    }

    public function copyFrom(PurchaseOrderHeader $sourcePurchaseOrderHeader): PurchaseOrderHeader
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $purchaseOrderHeader->setSupplier($sourcePurchaseOrderHeader->getSupplier());
        foreach ($sourcePurchaseOrderHeader->getPurchaseOrderDetails() as $sourcePurchaseOrderDetail) {
            $purchaseOrderDetail = new PurchaseOrderDetail();
            $purchaseOrderDetail->setMaterial($sourcePurchaseOrderDetail->getMaterial());
            $purchaseOrderDetail->setQuantity($sourcePurchaseOrderDetail->getQuantity());
            $purchaseOrderDetail->setUnit($sourcePurchaseOrderDetail->getUnit());
            $purchaseOrderDetail->setUnitPrice($sourcePurchaseOrderDetail->getUnitPrice());
            $purchaseOrderHeader->addPurchaseOrderDetail($purchaseOrderDetail);
        }
        return $purchaseOrderHeader;
    }
}
