<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\QualityControlSortingDetail;
use App\Entity\Production\QualityControlSortingHeader;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Production\QualityControlSortingHeaderRepository;
use App\Repository\Production\QualityControlSortingDetailRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Production\QualityControlSortingHeaderFormSupport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class QualityControlSortingHeaderFormService
{
    use QualityControlSortingHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private QualityControlSortingHeaderRepository $qualityControlSortingHeaderRepository;
    private QualityControlSortingDetailRepository $qualityControlSortingDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->qualityControlSortingHeaderRepository = $entityManager->getRepository(QualityControlSortingHeader::class);
        $this->qualityControlSortingDetailRepository = $entityManager->getRepository(QualityControlSortingDetail::class);
    }

    public function initialize(QualityControlSortingHeader $qualityControlSortingHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($qualityControlSortingHeader->getId())) {
            $qualityControlSortingHeader->setCreatedTransactionDateTime($datetime);
            $qualityControlSortingHeader->setCreatedTransactionUser($user);
        } else {
            $qualityControlSortingHeader->setModifiedTransactionDateTime($datetime);
            $qualityControlSortingHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(QualityControlSortingHeader $qualityControlSortingHeader, array $options = []): void
    {
        if ($qualityControlSortingHeader->getTransactionDate() !== null && $qualityControlSortingHeader->getId() === null) {
            $year = $qualityControlSortingHeader->getTransactionDate()->format('y');
            $month = $qualityControlSortingHeader->getTransactionDate()->format('m');
            $lastQualityControlSorting = $this->qualityControlSortingHeaderRepository->findRecentBy($year, $month);
            $currentQualityControlSorting = ($lastQualityControlSorting === null) ? $qualityControlSortingHeader : $lastQualityControlSorting;
            $qualityControlSortingHeader->setCodeNumberToNext($currentQualityControlSorting->getCodeNumber(), $year, $month);
        }
        
        foreach ($qualityControlSortingHeader->getQualityControlSortingDetails() as $i => $qualityControlSortingDetail) {
            $qualityControlSortingDetail->setIsCanceled($qualityControlSortingDetail->getSyncIsCanceled());
            $qualityControlSortingDetail->setQuantityOrder($qualityControlSortingDetail->getMasterOrderProductDetail()->getQuantityOrder());
            $qualityControlSortingDetail->setTotalQuantitySorting($qualityControlSortingDetail->getSyncTotalQuantitySorting());
            $qualityControlSortingDetail->setTotalQuantityReject($qualityControlSortingDetail->getSyncTotalQuantityReject());
            $qualityControlSortingDetail->setQuantityRemaining($qualityControlSortingDetail->getSyncQuantityRemaining());
            $qualityControlSortingDetail->setRejectPercentage($qualityControlSortingDetail->getSyncRejectPercentage());
        }
    }

    public function save(QualityControlSortingHeader $qualityControlSortingHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($qualityControlSortingHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->qualityControlSortingHeaderRepository->add($qualityControlSortingHeader);
            foreach ($qualityControlSortingHeader->getQualityControlSortingDetails() as $qualityControlSortingDetail) {
                $this->qualityControlSortingDetailRepository->add($qualityControlSortingDetail);
            }
            $this->entityManager->flush();
            
            $transactionLog = $this->buildTransactionLog($qualityControlSortingHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
}
