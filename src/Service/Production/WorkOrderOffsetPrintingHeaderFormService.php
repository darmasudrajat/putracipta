<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderOffsetPrintingHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Production\WorkOrderOffsetPrintingHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WorkOrderOffsetPrintingHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderOffsetPrintingHeaderRepository $workOrderOffsetPrintingHeaderRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->workOrderOffsetPrintingHeaderRepository = $entityManager->getRepository(WorkOrderOffsetPrintingHeader::class);
    }

    public function initialize(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderOffsetPrintingHeader->getId())) {
            $workOrderOffsetPrintingHeader->setCreatedTransactionDateTime($datetime);
            $workOrderOffsetPrintingHeader->setCreatedTransactionUser($user);
        } else {
            $workOrderOffsetPrintingHeader->setModifiedTransactionDateTime($datetime);
            $workOrderOffsetPrintingHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader, array $options = []): void
    {
        if ($workOrderOffsetPrintingHeader->getTransactionDate() !== null) {
            $year = $workOrderOffsetPrintingHeader->getTransactionDate()->format('y');
            $month = $workOrderOffsetPrintingHeader->getTransactionDate()->format('m');
            $lastWorkOrderOffsetPrintingHeader = $this->workOrderOffsetPrintingHeaderRepository->findRecentBy($year, $month);
            $currentWorkOrderOffsetPrintingHeader = ($lastWorkOrderOffsetPrintingHeader === null) ? $workOrderOffsetPrintingHeader : $lastWorkOrderOffsetPrintingHeader;
            $workOrderOffsetPrintingHeader->setCodeNumberToNext($currentWorkOrderOffsetPrintingHeader->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->workOrderOffsetPrintingHeaderRepository->add($workOrderOffsetPrintingHeader);
        $this->entityManager->flush();
    }
}
