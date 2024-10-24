<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Purchase\PurchaseOrderPaperHeaderFormSupport;
use App\Sync\Purchase\PurchaseOrderPaperHeaderFormSync;
use App\Util\Service\EntityResetUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseOrderPaperHeaderFormService
{
    use PurchaseOrderPaperHeaderFormSupport;

    private PurchaseOrderPaperHeaderFormSync $formSync;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private TransactionLogRepository $transactionLogRepository;
    private PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;

    public function __construct(RequestStack $requestStack, PurchaseOrderPaperHeaderFormSync $formSync, EntityManagerInterface $entityManager)
    {
        $this->formSync = $formSync;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->purchaseOrderPaperHeaderRepository = $entityManager->getRepository(PurchaseOrderPaperHeader::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
    }

    public function initialize(PurchaseOrderPaperHeader $purchaseOrderPaperHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (isset($options['cancelTransaction']) && $options['cancelTransaction'] === true) {
            $purchaseOrderPaperHeader->setIsCanceled(true);
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_CANCEL);
            $purchaseOrderPaperHeader->setCancelledTransactionDateTime($datetime);
            $purchaseOrderPaperHeader->setCancelledTransactionUser($user);
        } else {
            if (empty($purchaseOrderPaperHeader->getId())) {
                $purchaseOrderPaperHeader->setCreatedTransactionDateTime($datetime);
                $purchaseOrderPaperHeader->setCreatedTransactionUser($user);
            } else {
                $purchaseOrderPaperHeader->setModifiedTransactionDateTime($datetime);
                $purchaseOrderPaperHeader->setModifiedTransactionUser($user);
            }
            
            $purchaseOrderPaperHeader->setCodeNumberVersion($purchaseOrderPaperHeader->getCodeNumberVersion() + 1);
        }
    }

    public function finalize(PurchaseOrderPaperHeader $purchaseOrderPaperHeader, array $options = []): void
    {
        if (isset($options['cancelTransaction']) && $options['cancelTransaction'] === true) {
            EntityResetUtil::reset($this->formSync, $purchaseOrderPaperHeader);
        } else {
            foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
                EntityResetUtil::reset($this->formSync, $purchaseOrderPaperDetail);
            }
        }
        
        if ($purchaseOrderPaperHeader->getTransactionDate() !== null && $purchaseOrderPaperHeader->getId() === null) {
            $year = $purchaseOrderPaperHeader->getTransactionDate()->format('y');
            $month = $purchaseOrderPaperHeader->getTransactionDate()->format('m');
            $lastPurchaseOrderPaperHeader = $this->purchaseOrderPaperHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseOrderPaperHeader = ($lastPurchaseOrderPaperHeader === null) ? $purchaseOrderPaperHeader : $lastPurchaseOrderPaperHeader;
            $purchaseOrderPaperHeader->setCodeNumberToNext($currentPurchaseOrderPaperHeader->getCodeNumber(), $year, $month);

        }
        if ($purchaseOrderPaperHeader->getTaxMode() !== $purchaseOrderPaperHeader::TAX_MODE_NON_TAX) {
            $purchaseOrderPaperHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $purchaseOrderPaperHeader->setTaxPercentage(0);
        }
        
        foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
            $paper = $purchaseOrderPaperDetail->getPaper();
            $purchaseOrderPaperDetail->setIsCanceled($purchaseOrderPaperDetail->getSyncIsCanceled());
            $purchaseOrderPaperDetail->setIsTransactionClosed($purchaseOrderPaperDetail->getSyncIsTransactionClosed());
            $purchaseOrderPaperDetail->setLength($paper->getLength());
            $purchaseOrderPaperDetail->setWidth($paper->getWidth());
            $purchaseOrderPaperDetail->setWeight($paper->getWeight());
            if ($purchaseOrderPaperDetail->getApkiValue() > '0.00' || $purchaseOrderPaperDetail->getAssociationPrice() > '0.00') {
                $purchaseOrderPaperDetail->setWeightPrice($purchaseOrderPaperDetail->getSyncWeightPrice());
            }
            if ($purchaseOrderPaperDetail->getApkiValue() > '0.00' || $purchaseOrderPaperDetail->getAssociationPrice() > '0.00' || $purchaseOrderPaperDetail->getWeightPrice() > '0.00') {
                $purchaseOrderPaperDetail->setUnitPrice($purchaseOrderPaperDetail->getSyncUnitPrice());
            }
            $purchaseOrderPaperDetail->setRemainingReceive($purchaseOrderPaperDetail->getSyncRemainingReceive());
            $purchaseOrderPaperDetail->setUnitPriceBeforeTax($purchaseOrderPaperDetail->getSyncUnitPriceBeforeTax());
            $purchaseOrderPaperDetail->setTotal($purchaseOrderPaperDetail->getSyncTotal());
            
            if ($purchaseOrderPaperDetail->getRemainingReceive() === 0) {
                $purchaseOrderPaperDetail->setIsTransactionClosed(true);
            }
            
            if ($purchaseOrderPaperDetail->isIsTransactionClosed() === true or $purchaseOrderPaperDetail->isIsCanceled() === true) {
                $purchaseOrderPaperDetail->setRemainingReceive(0);
            }
            
            $purchaseRequestPaperDetail = $purchaseOrderPaperDetail->getPurchaseRequestPaperDetail();
            if ($purchaseRequestPaperDetail !== null) {
                $purchaseRequestPaperDetail->setTransactionStatus(PurchaseRequestPaperDetail::TRANSACTION_STATUS_PURCHASE);
            }
        }
        $supplier = $purchaseOrderPaperHeader->getSupplier();
        $purchaseOrderPaperHeader->setCurrency($supplier === null ? null : $supplier->getCurrency());
        $purchaseOrderPaperHeader->setSubTotal($purchaseOrderPaperHeader->getSyncSubTotal());
        $purchaseOrderPaperHeader->setTaxNominal($purchaseOrderPaperHeader->getSyncTaxNominal());
        $purchaseOrderPaperHeader->setGrandTotal($purchaseOrderPaperHeader->getSyncGrandTotal());
        $purchaseOrderPaperHeader->setTotalRemainingReceive($purchaseOrderPaperHeader->getSyncTotalRemainingReceive());
        
        $purchaseOrderPaperList = [];
        foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
            if ($purchaseOrderPaperDetail->isIsCanceled() == false) {
                $paper = $purchaseOrderPaperDetail->getPaper();
                $purchaseOrderPaperList[] = $paper->getName();
            }
        }
        $purchaseOrderPaperUniqueList = array_unique(explode(', ', implode(', ', $purchaseOrderPaperList)));
        $purchaseOrderPaperHeader->setPurchaseOrderPaperList(implode(', ', $purchaseOrderPaperUniqueList));
    }

    public function save(PurchaseOrderPaperHeader $purchaseOrderPaperHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($purchaseOrderPaperHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader);
            foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
                $this->purchaseOrderPaperDetailRepository->add($purchaseOrderPaperDetail);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($purchaseOrderPaperHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
    
    public function createSyncView(): array
    {
        return $this->formSync->getView();
    }

    public function copyFrom(PurchaseOrderPaperHeader $sourcePurchaseOrderPaperHeader): PurchaseOrderPaperHeader
    {
        $purchaseOrderPaperHeader = new PurchaseOrderPaperHeader();
        $purchaseOrderPaperHeader->setSupplier($sourcePurchaseOrderPaperHeader->getSupplier());
        foreach ($sourcePurchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $sourcePurchaseOrderPaperDetail) {
            $purchaseOrderPaperDetail = new PurchaseOrderPaperDetail();
            $purchaseOrderPaperDetail->setPaper($sourcePurchaseOrderPaperDetail->getPaper());
            $purchaseOrderPaperDetail->setLength($sourcePurchaseOrderPaperDetail->getLength());
            $purchaseOrderPaperDetail->setWidth($sourcePurchaseOrderPaperDetail->getWidth());
            $purchaseOrderPaperDetail->setWeight($sourcePurchaseOrderPaperDetail->getWeight());
            $purchaseOrderPaperDetail->setQuantity($sourcePurchaseOrderPaperDetail->getQuantity());
            $purchaseOrderPaperDetail->setUnit($sourcePurchaseOrderPaperDetail->getUnit());
            $purchaseOrderPaperDetail->setApkiValue($sourcePurchaseOrderPaperDetail->getApkiValue());
            $purchaseOrderPaperDetail->setAssociationPrice($sourcePurchaseOrderPaperDetail->getAssociationPrice());
            $purchaseOrderPaperDetail->setWeightPrice($sourcePurchaseOrderPaperDetail->getWeightPrice());
            $purchaseOrderPaperDetail->setUnitPrice($sourcePurchaseOrderPaperDetail->getUnitPrice());
            $purchaseOrderPaperHeader->addPurchaseOrderPaperDetail($purchaseOrderPaperDetail);
        }
        return $purchaseOrderPaperHeader;
    }
}
