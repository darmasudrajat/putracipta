<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Paper;
use App\Entity\Support\Idempotent;
use App\Repository\Master\PaperRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaperFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private PaperRepository $paperRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->paperRepository = $entityManager->getRepository(Paper::class);
    }

    public function finalize(Paper $paper, array $options = []): void
    {
        $materialSubCategory = $options['materialSubCategory'];
        $weight = $options['weight'];
        $type = $options['type'];
        if ($paper->getId() === null || $materialSubCategory !== $paper->getMaterialSubCategory() || $weight !== $paper->getWeight() || $type !== $paper->getType()) {
            $lastPaper = $this->paperRepository->findRecentBy($paper->getMaterialSubCategory(), $paper->getWeight(), $paper->getType());
            $currentPaper = ($lastPaper === null) ? $paper : $lastPaper;
            $lastCode = ($lastPaper === null) ? 0 : $currentPaper->getCode();
            $paper->setCode($lastCode + 1);
        }
    }
    
    public function save(Paper $paper, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->paperRepository->add($paper);
        $this->entityManager->flush();
    }
}
