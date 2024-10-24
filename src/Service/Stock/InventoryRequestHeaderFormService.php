<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\InventoryRequestMaterialDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use App\Entity\Stock\InventoryRequestHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Stock\InventoryRequestMaterialDetailRepository;
use App\Repository\Stock\InventoryRequestPaperDetailRepository;
use App\Repository\Stock\InventoryRequestHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class InventoryRequestHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private InventoryRequestHeaderRepository $inventoryRequestHeaderRepository;
    private InventoryRequestMaterialDetailRepository $inventoryRequestMaterialDetailRepository;
    private InventoryRequestPaperDetailRepository $inventoryRequestPaperDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->inventoryRequestHeaderRepository = $entityManager->getRepository(InventoryRequestHeader::class);
        $this->inventoryRequestMaterialDetailRepository = $entityManager->getRepository(InventoryRequestMaterialDetail::class);
        $this->inventoryRequestPaperDetailRepository = $entityManager->getRepository(InventoryRequestPaperDetail::class);
    }

    public function initialize(InventoryRequestHeader $inventoryRequestHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($inventoryRequestHeader->getId())) {
            $inventoryRequestHeader->setCreatedTransactionDateTime($datetime);
            $inventoryRequestHeader->setCreatedTransactionUser($user);
        } else {
            $inventoryRequestHeader->setModifiedTransactionDateTime($datetime);
            $inventoryRequestHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(InventoryRequestHeader $inventoryRequestHeader, array $options = []): void
    {
        if ($inventoryRequestHeader->getTransactionDate() !== null && $inventoryRequestHeader->getId() === null) {
            $year = $inventoryRequestHeader->getTransactionDate()->format('y');
            $month = $inventoryRequestHeader->getTransactionDate()->format('m');
            $lastInventoryRequestHeader = $this->inventoryRequestHeaderRepository->findRecentBy($year, $month);
            $currentInventoryRequestHeader = ($lastInventoryRequestHeader === null) ? $inventoryRequestHeader : $lastInventoryRequestHeader;
            $inventoryRequestHeader->setCodeNumberToNext($currentInventoryRequestHeader->getCodeNumber(), $year, $month);
        }
        
        foreach ($inventoryRequestHeader->getInventoryRequestMaterialDetails() as $inventoryRequestMaterialDetail) {
            $material = $inventoryRequestMaterialDetail->getMaterial();
            $inventoryRequestMaterialDetail->setIsCanceled($inventoryRequestMaterialDetail->getSyncIsCanceled());
            $inventoryRequestMaterialDetail->setUnit($material->getUnit());
            $inventoryRequestMaterialDetail->setQuantityRemaining($inventoryRequestMaterialDetail->getSyncQuantityRemaining());
        }
        foreach ($inventoryRequestHeader->getInventoryRequestPaperDetails() as $inventoryRequestPaperDetail) {
            $paper = $inventoryRequestPaperDetail->getPaper();
            $inventoryRequestPaperDetail->setIsCanceled($inventoryRequestPaperDetail->getSyncIsCanceled());
            $inventoryRequestPaperDetail->setUnit($paper->getUnit());
            $inventoryRequestPaperDetail->setQuantityRemaining($inventoryRequestPaperDetail->getSyncQuantityRemaining());
        }
        $inventoryRequestHeader->setTotalQuantity($inventoryRequestHeader->getSyncTotalQuantity());
        $inventoryRequestHeader->setTotalQuantityRelease($inventoryRequestHeader->getSyncTotalQuantityRelease());
        $inventoryRequestHeader->setTotalQuantityRemaining($inventoryRequestHeader->getSyncTotalQuantityRemaining());
        
        $inventoryRequestItemList = [];
        foreach ($inventoryRequestHeader->getInventoryRequestMaterialDetails() as $inventoryRequestMaterialDetail) {
            $material = $inventoryRequestMaterialDetail->getMaterial();
            $inventoryRequestItemList[] = $material->getName();
        }
        foreach ($inventoryRequestHeader->getInventoryRequestPaperDetails() as $inventoryRequestPaperDetail) {
            $paper = $inventoryRequestPaperDetail->getPaper();
            $inventoryRequestItemList[] = $paper->getName();
        }
        $inventoryRequestItemUniqueList = array_unique(explode(', ', implode(', ', $inventoryRequestItemList)));
        $inventoryRequestHeader->setInventoryRequestProductList(implode(', ', $inventoryRequestItemUniqueList));
    }

    public function save(InventoryRequestHeader $inventoryRequestHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->inventoryRequestHeaderRepository->add($inventoryRequestHeader);
        foreach ($inventoryRequestHeader->getInventoryRequestMaterialDetails() as $inventoryRequestMaterialDetail) {
            $this->inventoryRequestMaterialDetailRepository->add($inventoryRequestMaterialDetail);
        }
        foreach ($inventoryRequestHeader->getInventoryRequestPaperDetails() as $inventoryRequestPaperDetail) {
            $this->inventoryRequestPaperDetailRepository->add($inventoryRequestPaperDetail);
        }
        $this->entityManager->flush();
    }
}
