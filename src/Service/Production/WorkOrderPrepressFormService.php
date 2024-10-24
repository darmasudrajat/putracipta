<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderPrepress;
use App\Entity\Support\Idempotent;
use App\Repository\Production\WorkOrderPrepressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WorkOrderPrepressFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderPrepressRepository $workOrderPrepressRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->workOrderPrepressRepository = $entityManager->getRepository(WorkOrderPrepress::class);
    }

    public function initialize(WorkOrderPrepress $workOrderPrepress, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderPrepress->getId())) {
            $workOrderPrepress->setCreatedTransactionDateTime($datetime);
            $workOrderPrepress->setCreatedTransactionUser($user);
        } else {
            $workOrderPrepress->setModifiedTransactionDateTime($datetime);
            $workOrderPrepress->setModifiedTransactionUser($user);
        }
    }

    public function finalize(WorkOrderPrepress $workOrderPrepress, array $options = []): void
    {
        if ($workOrderPrepress->getTransactionDate() !== null) {
            $year = $workOrderPrepress->getTransactionDate()->format('y');
            $month = $workOrderPrepress->getTransactionDate()->format('m');
            $lastWorkOrderPrepress = $this->workOrderPrepressRepository->findRecentBy($year, $month);
            $currentWorkOrderPrepress = ($lastWorkOrderPrepress === null) ? $workOrderPrepress : $lastWorkOrderPrepress;
            $workOrderPrepress->setCodeNumberToNext($currentWorkOrderPrepress->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderPrepress $workOrderPrepress, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->workOrderPrepressRepository->add($workOrderPrepress);
        $this->entityManager->flush();
    }
}
