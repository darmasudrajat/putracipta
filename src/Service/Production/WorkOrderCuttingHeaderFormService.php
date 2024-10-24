<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderCuttingHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Production\WorkOrderCuttingHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WorkOrderCuttingHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderCuttingHeaderRepository $workOrderCuttingHeaderRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->workOrderCuttingHeaderRepository = $entityManager->getRepository(WorkOrderCuttingHeader::class);
    }

    public function initialize(WorkOrderCuttingHeader $workOrderCuttingHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderCuttingHeader->getId())) {
            $workOrderCuttingHeader->setCreatedTransactionDateTime($datetime);
            $workOrderCuttingHeader->setCreatedTransactionUser($user);
        } else {
            $workOrderCuttingHeader->setModifiedTransactionDateTime($datetime);
            $workOrderCuttingHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(WorkOrderCuttingHeader $workOrderCuttingHeader, array $options = []): void
    {
        if ($workOrderCuttingHeader->getTransactionDate() !== null) {
            $year = $workOrderCuttingHeader->getTransactionDate()->format('y');
            $month = $workOrderCuttingHeader->getTransactionDate()->format('m');
            $lastWorkOrderCuttingHeader = $this->workOrderCuttingHeaderRepository->findRecentBy($year, $month);
            $currentWorkOrderCuttingHeader = ($lastWorkOrderCuttingHeader === null) ? $workOrderCuttingHeader : $lastWorkOrderCuttingHeader;
            $workOrderCuttingHeader->setCodeNumberToNext($currentWorkOrderCuttingHeader->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderCuttingHeader $workOrderCuttingHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->workOrderCuttingHeaderRepository->add($workOrderCuttingHeader);
        $this->entityManager->flush();
    }
}
