<?php

namespace App\Service\Accounting;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Accounting\AccountingLedger;
use App\Entity\Accounting\JournalVoucherDetail;
use App\Entity\Accounting\JournalVoucherHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Accounting\AccountingLedgerRepository;
use App\Repository\Accounting\JournalVoucherDetailRepository;
use App\Repository\Accounting\JournalVoucherHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Util\Service\AccountingLedgerUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class JournalVoucherHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private AccountingLedgerRepository $accountingLedgerRepository;
    private JournalVoucherHeaderRepository $journalVoucherHeaderRepository;
    private JournalVoucherDetailRepository $journalVoucherDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->accountingLedgerRepository = $entityManager->getRepository(AccountingLedger::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->journalVoucherHeaderRepository = $entityManager->getRepository(JournalVoucherHeader::class);
        $this->journalVoucherDetailRepository = $entityManager->getRepository(JournalVoucherDetail::class);
    }

    public function initialize(JournalVoucherHeader $journalVoucherHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($journalVoucherHeader->getId())) {
            $journalVoucherHeader->setCreatedAccountingDateTime($datetime);
            $journalVoucherHeader->setCreatedAccountingUser($user);
        } else {
            $journalVoucherHeader->setModifiedAccountingDateTime($datetime);
            $journalVoucherHeader->setModifiedAccountingUser($user);
        }
    }

    public function finalize(JournalVoucherHeader $journalVoucherHeader, array $options = []): void
    {
        if ($journalVoucherHeader->getTransactionDate() !== null && $journalVoucherHeader->getId() === null) {
            $year = $journalVoucherHeader->getTransactionDate()->format('y');
            $month = $journalVoucherHeader->getTransactionDate()->format('m');
            $lastJournalVoucherHeader = $this->journalVoucherHeaderRepository->findRecentBy($year, $month);
            $currentJournalVoucherHeader = ($lastJournalVoucherHeader === null) ? $journalVoucherHeader : $lastJournalVoucherHeader;
            $journalVoucherHeader->setCodeNumberToNext($currentJournalVoucherHeader->getCodeNumber(), $year, $month);
        }
    }

    public function save(JournalVoucherHeader $journalVoucherHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->journalVoucherHeaderRepository->add($journalVoucherHeader);
        foreach ($journalVoucherHeader->getJournalVoucherDetails() as $journalVoucherDetail) {
            $this->journalVoucherDetailRepository->add($journalVoucherDetail);
        }
        $this->addAccountingLedgers($journalVoucherHeader);
        $this->entityManager->flush();
    }
    
    private function addAccountingLedgers(JournalVoucherHeader $journalVoucherHeader): void
    {
        AccountingLedgerUtil::reverseOldData($this->accountingLedgerRepository, $journalVoucherHeader);
        $journalVoucherDetails = $journalVoucherHeader->getJournalVoucherDetails()->toArray();
        AccountingLedgerUtil::addNewData($this->accountingLedgerRepository, $journalVoucherHeader, $journalVoucherDetails, function($newLedger, $journalVoucherDetail) use ($journalVoucherHeader) {
            $account = $journalVoucherDetail->getAccount();
            $newLedger->setTransactionSubject($journalVoucherDetail->getMemo());
            $newLedger->setAccount($account);
            $newLedger->setDebitAmount($journalVoucherDetail->getDebitAmount());
            $newLedger->setCreditAmount($journalVoucherDetail->getCreditAmount());
        });
    }
}
