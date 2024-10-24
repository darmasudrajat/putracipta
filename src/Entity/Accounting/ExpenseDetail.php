<?php

namespace App\Entity\Accounting;

use App\Entity\AccountingDetail;
use App\Entity\Master\Account;
use App\Repository\Accounting\ExpenseDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpenseDetailRepository::class)]
#[ORM\Table(name: 'accounting_expense_detail')]
class ExpenseDetail extends AccountingDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $description = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $amount = '0.00';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'expenseDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExpenseHeader $expenseHeader = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

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

    public function getExpenseHeader(): ?ExpenseHeader
    {
        return $this->expenseHeader;
    }

    public function setExpenseHeader(?ExpenseHeader $expenseHeader): self
    {
        $this->expenseHeader = $expenseHeader;

        return $this;
    }
}
