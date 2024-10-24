<?php

namespace App\Entity\Accounting;

use App\Entity\AccountingDetail;
use App\Entity\Master\Account;
use App\Repository\Accounting\DepositDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepositDetailRepository::class)]
#[ORM\Table(name: 'accounting_deposit_detail')]
class DepositDetail extends AccountingDetail
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

    #[ORM\ManyToOne(inversedBy: 'depositDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DepositHeader $depositHeader = null;

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

    public function getDepositHeader(): ?DepositHeader
    {
        return $this->depositHeader;
    }

    public function setDepositHeader(?DepositHeader $depositHeader): self
    {
        $this->depositHeader = $depositHeader;

        return $this;
    }
}
