<?php

namespace App\Entity\Accounting;

use App\Entity\Master\Account;
use App\Repository\Accounting\AccountingLedgerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountingLedgerRepository::class)]
#[ORM\Table(name: 'accounting_accounting_ledger')]
class AccountingLedger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $transactionType = '';

    #[ORM\Column(length: 100)]
    private ?string $transactionSubject = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $debitAmount = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $creditAmount = '0.00';

    #[ORM\ManyToOne]
    private ?Account $account = null;

    #[ORM\Column]
    private ?int $transactionCodeNumberOrdinal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $transactionCodeNumberMonth = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $transactionCodeNumberYear = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    #[ORM\Column]
    private ?bool $isReversed = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAccountingLedgerDateTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getTransactionSubject(): ?string
    {
        return $this->transactionSubject;
    }

    public function setTransactionSubject(string $transactionSubject): self
    {
        $this->transactionSubject = $transactionSubject;

        return $this;
    }

    public function getDebitAmount(): ?string
    {
        return $this->debitAmount;
    }

    public function setDebitAmount(string $debitAmount): self
    {
        $this->debitAmount = $debitAmount;

        return $this;
    }

    public function getCreditAmount(): ?string
    {
        return $this->creditAmount;
    }

    public function setCreditAmount(string $creditAmount): self
    {
        $this->creditAmount = $creditAmount;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getTransactionCodeNumberOrdinal(): ?int
    {
        return $this->transactionCodeNumberOrdinal;
    }

    public function setTransactionCodeNumberOrdinal(int $transactionCodeNumberOrdinal): self
    {
        $this->transactionCodeNumberOrdinal = $transactionCodeNumberOrdinal;

        return $this;
    }

    public function getTransactionCodeNumberMonth(): ?int
    {
        return $this->transactionCodeNumberMonth;
    }

    public function setTransactionCodeNumberMonth(int $transactionCodeNumberMonth): self
    {
        $this->transactionCodeNumberMonth = $transactionCodeNumberMonth;

        return $this;
    }

    public function getTransactionCodeNumberYear(): ?int
    {
        return $this->transactionCodeNumberYear;
    }

    public function setTransactionCodeNumberYear(int $transactionCodeNumberYear): self
    {
        $this->transactionCodeNumberYear = $transactionCodeNumberYear;

        return $this;
    }

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(\DateTimeInterface $transactionDate): self
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function isIsReversed(): ?bool
    {
        return $this->isReversed;
    }

    public function setIsReversed(bool $isReversed): self
    {
        $this->isReversed = $isReversed;

        return $this;
    }

    public function getCreatedAccountingLedgerDateTime(): ?\DateTimeInterface
    {
        return $this->createdAccountingLedgerDateTime;
    }

    public function setCreatedAccountingLedgerDateTime(?\DateTimeInterface $createdAccountingLedgerDateTime): self
    {
        $this->createdAccountingLedgerDateTime = $createdAccountingLedgerDateTime;

        return $this;
    }
}
