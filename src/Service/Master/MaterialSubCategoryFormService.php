<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\MaterialSubCategory;
use App\Entity\Support\Idempotent;
use App\Repository\Master\MaterialSubCategoryRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MaterialSubCategoryFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private MaterialSubCategoryRepository $materialSubCategoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->materialSubCategoryRepository = $entityManager->getRepository(MaterialSubCategory::class);
    }

    public function save(MaterialSubCategory $materialSubCategory, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->materialSubCategoryRepository->add($materialSubCategory);
        $this->entityManager->flush();
    }
}
