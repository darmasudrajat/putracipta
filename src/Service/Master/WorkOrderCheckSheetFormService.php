<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\WorkOrderCheckSheet;
use App\Entity\Support\Idempotent;
use App\Repository\Master\WorkOrderCheckSheetRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WorkOrderCheckSheetFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private WorkOrderCheckSheetRepository $workOrderCheckSheetRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->workOrderCheckSheetRepository = $entityManager->getRepository(WorkOrderCheckSheet::class);
    }

    public function save(WorkOrderCheckSheet $workOrderCheckSheet, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->workOrderCheckSheetRepository->add($workOrderCheckSheet);
        $this->entityManager->flush();
    }
}
