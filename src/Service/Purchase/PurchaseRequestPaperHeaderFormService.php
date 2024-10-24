<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\Purchase\PurchaseRequestPaperHeader;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Purchase\PurchaseRequestPaperDetailRepository;
use App\Repository\Purchase\PurchaseRequestPaperHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Purchase\PurchaseRequestPaperHeaderFormSupport;
use App\Sync\Purchase\PurchaseRequestPaperHeaderFormSync;
use App\Util\Service\EntityResetUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseRequestPaperHeaderFormService
{
    use PurchaseRequestPaperHeaderFormSupport;
    
    private PurchaseRequestPaperHeaderFormSync $formSync;
    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository;
    private PurchaseRequestPaperDetailRepository $purchaseRequestPaperDetailRepository;

    public function __construct(RequestStack $requestStack, PurchaseRequestPaperHeaderFormSync $formSync, EntityManagerInterface $entityManager)
    {
        $this->formSync = $formSync;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->purchaseRequestPaperHeaderRepository = $entityManager->getRepository(PurchaseRequestPaperHeader::class);
        $this->purchaseRequestPaperDetailRepository = $entityManager->getRepository(PurchaseRequestPaperDetail::class);
    }

    public function initialize(PurchaseRequestPaperHeader $purchaseRequestPaperHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (isset($options['cancelTransaction']) && $options['cancelTransaction'] === true) {
            $purchaseRequestPaperHeader->setIsCanceled(true);
            $purchaseRequestPaperHeader->setTransactionStatus(PurchaseRequestPaperHeader::TRANSACTION_STATUS_CANCEL);
            $purchaseRequestPaperHeader->setCancelledTransactionDateTime($datetime);
            $purchaseRequestPaperHeader->setCancelledTransactionUser($user);
        } else {
            if (empty($purchaseRequestPaperHeader->getId())) {
                $purchaseRequestPaperHeader->setCreatedTransactionDateTime($datetime);
                $purchaseRequestPaperHeader->setCreatedTransactionUser($user);
            } else {
                $purchaseRequestPaperHeader->setModifiedTransactionDateTime($datetime);
                $purchaseRequestPaperHeader->setModifiedTransactionUser($user);
            }

            $purchaseRequestPaperHeader->setCodeNumberVersion($purchaseRequestPaperHeader->getCodeNumberVersion() + 1);
        }
    }

    public function finalize(PurchaseRequestPaperHeader $purchaseRequestPaperHeader, array $options = []): void
    {
        if (isset($options['cancelTransaction']) && $options['cancelTransaction'] === true) {
            EntityResetUtil::reset($this->formSync, $purchaseRequestPaperHeader);
        } else {
            foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
                EntityResetUtil::reset($this->formSync, $purchaseRequestPaperDetail);
            }
        }
        
        if ($purchaseRequestPaperHeader->getTransactionDate() !== null && $purchaseRequestPaperHeader->getId() === null) {
            $year = $purchaseRequestPaperHeader->getTransactionDate()->format('y');
            $month = $purchaseRequestPaperHeader->getTransactionDate()->format('m');
            $lastPurchaseRequestPaperHeader = $this->purchaseRequestPaperHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseRequestPaperHeader = ($lastPurchaseRequestPaperHeader === null) ? $purchaseRequestPaperHeader : $lastPurchaseRequestPaperHeader;
            $purchaseRequestPaperHeader->setCodeNumberToNext($currentPurchaseRequestPaperHeader->getCodeNumber(), $year, $month);

        }
        foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
            $purchaseRequestPaperDetail->setIsCanceled($purchaseRequestPaperDetail->getSyncIsCanceled());
            $purchaseRequestPaperDetail->setTransactionStatus(PurchaseRequestPaperDetail::TRANSACTION_STATUS_OPEN);
        }
        $purchaseRequestPaperHeader->setTotalQuantity($purchaseRequestPaperHeader->getSyncTotalQuantity());
        
        $purchaseRequestPaperList = [];
        foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
            if ($purchaseRequestPaperDetail->isIsCanceled() == false) {
                $paper = $purchaseRequestPaperDetail->getPaper();
                $purchaseRequestPaperList[] = $paper->getName();
            }
        }
        $purchaseRequestPaperUniqueList = array_unique(explode(', ', implode(', ', $purchaseRequestPaperList)));
        $purchaseRequestPaperHeader->setPurchaseRequestPaperList(implode(', ', $purchaseRequestPaperUniqueList));
    }

    public function save(PurchaseRequestPaperHeader $purchaseRequestPaperHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($purchaseRequestPaperHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader);
            foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
                $this->purchaseRequestPaperDetailRepository->add($purchaseRequestPaperDetail);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($purchaseRequestPaperHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
    
    public function createSyncView(): array
    {
        return $this->formSync->getView();
    }
}
