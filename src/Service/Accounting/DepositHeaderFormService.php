<?php

namespace App\Service\Accounting;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Accounting\AccountingLedger;
use App\Entity\Accounting\DepositDetail;
use App\Entity\Accounting\DepositHeader;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Accounting\AccountingLedgerRepository;
use App\Repository\Accounting\DepositDetailRepository;
use App\Repository\Accounting\DepositHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Accounting\DepositHeaderFormSupport;
use App\Util\Service\AccountingLedgerUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DepositHeaderFormService
{
    use DepositHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private AccountingLedgerRepository $accountingLedgerRepository;
    private DepositHeaderRepository $depositHeaderRepository;
    private DepositDetailRepository $depositDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->accountingLedgerRepository = $entityManager->getRepository(AccountingLedger::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->depositHeaderRepository = $entityManager->getRepository(DepositHeader::class);
        $this->depositDetailRepository = $entityManager->getRepository(DepositDetail::class);
    }

    public function initialize(DepositHeader $depositHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($depositHeader->getId())) {
            $depositHeader->setCreatedTransactionDateTime($datetime);
            $depositHeader->setCreatedTransactionUser($user);
        } else {
            $depositHeader->setModifiedTransactionDateTime($datetime);
            $depositHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(DepositHeader $depositHeader, array $options = []): void
    {
        if ($depositHeader->getTransactionDate() !== null && $depositHeader->getId() === null) {
            $year = $depositHeader->getTransactionDate()->format('y');
            $month = $depositHeader->getTransactionDate()->format('m');
            $lastDepositHeader = $this->depositHeaderRepository->findRecentBy($year, $month);
            $currentDepositHeader = ($lastDepositHeader === null) ? $depositHeader : $lastDepositHeader;
            $depositHeader->setCodeNumberToNext($currentDepositHeader->getCodeNumber(), $year, $month);

        }
        $depositHeader->setTotalAmount($depositHeader->getSyncTotalAmount());        
    }

    public function save(DepositHeader $depositHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($depositHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->depositHeaderRepository->add($depositHeader);
            foreach ($depositHeader->getDepositDetails() as $depositDetail) {
                $this->depositDetailRepository->add($depositDetail);
            }
            $this->addAccountingLedgers($depositHeader);
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($depositHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
    
    private function addAccountingLedgers(DepositHeader $depositHeader): void
    {
        AccountingLedgerUtil::reverseOldData($this->accountingLedgerRepository, $depositHeader);
        $depositDetails = $depositHeader->getDepositDetails()->toArray();
        AccountingLedgerUtil::addNewData($this->accountingLedgerRepository, $depositHeader, $depositDetails, function($newLedger, $depositDetail) use ($depositHeader) {
            $account = $depositDetail->getAccount();
            $newLedger->setTransactionSubject($depositDetail->getMemo());
            $newLedger->setAccount($account);
            $newLedger->setCreditAmount($depositDetail->getAmount());
        });
    }
}
