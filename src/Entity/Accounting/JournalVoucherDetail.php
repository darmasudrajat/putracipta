<?php

namespace App\Entity\Accounting;

use App\Entity\AccountingDetail;
use App\Entity\Master\Account;
use App\Repository\Accounting\JournalVoucherDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalVoucherDetailRepository::class)]
#[ORM\Table(name: 'accounting_journal_voucher_detail')]
class JournalVoucherDetail extends AccountingDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $debitAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $creditAmount = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'journalVoucherDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JournalVoucherHeader $journalVoucherHeader = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getJournalVoucherHeader(): ?JournalVoucherHeader
    {
        return $this->journalVoucherHeader;
    }

    public function setJournalVoucherHeader(?JournalVoucherHeader $journalVoucherHeader): self
    {
        $this->journalVoucherHeader = $journalVoucherHeader;

        return $this;
    }
}
