<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderColorMixing;
use App\Entity\Support\Idempotent;
use App\Repository\Production\WorkOrderColorMixingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WorkOrderColorMixingFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderColorMixingRepository $workOrderColorMixingRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->workOrderColorMixingRepository = $entityManager->getRepository(WorkOrderColorMixing::class);
    }

    public function initialize(WorkOrderColorMixing $workOrderColorMixing, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderColorMixing->getId())) {
            $workOrderColorMixing->setCreatedTransactionDateTime($datetime);
            $workOrderColorMixing->setCreatedTransactionUser($user);
        } else {
            $workOrderColorMixing->setModifiedTransactionDateTime($datetime);
            $workOrderColorMixing->setModifiedTransactionUser($user);
        }
    }

    public function finalize(WorkOrderColorMixing $workOrderColorMixing, array $options = []): void
    {
        if ($workOrderColorMixing->getTransactionDate() !== null) {
            $year = $workOrderColorMixing->getTransactionDate()->format('y');
            $month = $workOrderColorMixing->getTransactionDate()->format('m');
            $lastWorkOrderColorMixing = $this->workOrderColorMixingRepository->findRecentBy($year, $month);
            $currentWorkOrderColorMixing = ($lastWorkOrderColorMixing === null) ? $workOrderColorMixing : $lastWorkOrderColorMixing;
            $workOrderColorMixing->setCodeNumberToNext($currentWorkOrderColorMixing->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderColorMixing $workOrderColorMixing, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->workOrderColorMixingRepository->add($workOrderColorMixing);
        $this->entityManager->flush();
    }
}
