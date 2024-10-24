<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\WorkOrderDistribution;
use App\Entity\Support\Idempotent;
use App\Repository\Master\WorkOrderDistributionRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WorkOrderDistributionFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private WorkOrderDistributionRepository $workOrderDistributionRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->workOrderDistributionRepository = $entityManager->getRepository(WorkOrderDistribution::class);
    }

    public function save(WorkOrderDistribution $workOrderDistribution, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->workOrderDistributionRepository->add($workOrderDistribution);
        $this->entityManager->flush();
    }
}
