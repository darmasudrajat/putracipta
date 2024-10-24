<?php

namespace App\Service\Accounting;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Accounting\AccountingLedger;
use App\Entity\Accounting\ExpenseDetail;
use App\Entity\Accounting\ExpenseHeader;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Accounting\AccountingLedgerRepository;
use App\Repository\Accounting\ExpenseDetailRepository;
use App\Repository\Accounting\ExpenseHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Accounting\ExpenseHeaderFormSupport;
use App\Util\Service\AccountingLedgerUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ExpenseHeaderFormService
{
    use ExpenseHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private AccountingLedgerRepository $accountingLedgerRepository;
    private ExpenseHeaderRepository $expenseHeaderRepository;
    private ExpenseDetailRepository $expenseDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->accountingLedgerRepository = $entityManager->getRepository(AccountingLedger::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->expenseHeaderRepository = $entityManager->getRepository(ExpenseHeader::class);
        $this->expenseDetailRepository = $entityManager->getRepository(ExpenseDetail::class);
    }

    public function initialize(ExpenseHeader $expenseHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($expenseHeader->getId())) {
            $expenseHeader->setCreatedTransactionDateTime($datetime);
            $expenseHeader->setCreatedTransactionUser($user);
        } else {
            $expenseHeader->setModifiedTransactionDateTime($datetime);
            $expenseHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(ExpenseHeader $expenseHeader, array $options = []): void
    {
        if ($expenseHeader->getTransactionDate() !== null && $expenseHeader->getId() === null) {
            $year = $expenseHeader->getTransactionDate()->format('y');
            $month = $expenseHeader->getTransactionDate()->format('m');
            $lastExpenseHeader = $this->expenseHeaderRepository->findRecentBy($year, $month);
            $currentExpenseHeader = ($lastExpenseHeader === null) ? $expenseHeader : $lastExpenseHeader;
            $expenseHeader->setCodeNumberToNext($currentExpenseHeader->getCodeNumber(), $year, $month);

        }
        $expenseHeader->setTotalAmount($expenseHeader->getSyncTotalAmount());        
    }

    public function save(ExpenseHeader $expenseHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($expenseHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->expenseHeaderRepository->add($expenseHeader);
            foreach ($expenseHeader->getExpenseDetails() as $expenseDetail) {
                $this->expenseDetailRepository->add($expenseDetail);
            }
            $this->addAccountingLedgers($expenseHeader);
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($expenseHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
    
    private function addAccountingLedgers(ExpenseHeader $expenseHeader): void
    {
        AccountingLedgerUtil::reverseOldData($this->accountingLedgerRepository, $expenseHeader);
        $expenseDetails = $expenseHeader->getExpenseDetails()->toArray();
        AccountingLedgerUtil::addNewData($this->accountingLedgerRepository, $expenseHeader, $expenseDetails, function($newLedger, $expenseDetail) use ($expenseHeader) {
            $account = $expenseDetail->getAccount();
            $newLedger->setTransactionSubject($expenseDetail->getMemo());
            $newLedger->setAccount($account);
            $newLedger->setDebitAmount($expenseDetail->getAmount());
        });
    }
}
