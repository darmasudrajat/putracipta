<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Account;
use App\Entity\Support\Idempotent;
use App\Repository\Master\AccountRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AccountFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private AccountRepository $accountRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->accountRepository = $entityManager->getRepository(Account::class);
    }

    public function save(Account $account, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->accountRepository->add($account);
        $this->entityManager->flush();
    }
}
