<?php

namespace App\Entity\Support;

use App\Repository\Support\TransactionLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionLogRepository::class)]
#[ORM\Table(name: 'support_transaction_log')]
class TransactionLog
{
    public const MONTH_ROMAN_NUMERALS = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $codeNumberOrdinal = 0;

    #[ORM\Column]
    private ?int $codeNumberMonth = 0;

    #[ORM\Column]
    private ?int $codeNumberYear = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $logDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $logTime = null;

    #[ORM\Column(length: 60)]
    private ?string $entityName = '';

    #[ORM\Column]
    private ?int $entityId = 0;

    #[ORM\Column(nullable: true)]
    private array $newData = [];

    public function getCodeNumber(): string
    {
        $numerals = self::MONTH_ROMAN_NUMERALS;

        return sprintf('%04d/%s/%s/%02d', intval($this->codeNumberOrdinal), (new $this->entityName)->getCodeNumberConstant(), $numerals[intval($this->codeNumberMonth)], intval($this->codeNumberYear));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeNumberOrdinal(): ?int
    {
        return $this->codeNumberOrdinal;
    }

    public function setCodeNumberOrdinal(int $codeNumberOrdinal): self
    {
        $this->codeNumberOrdinal = $codeNumberOrdinal;

        return $this;
    }

    public function getCodeNumberMonth(): ?int
    {
        return $this->codeNumberMonth;
    }

    public function setCodeNumberMonth(int $codeNumberMonth): self
    {
        $this->codeNumberMonth = $codeNumberMonth;

        return $this;
    }

    public function getCodeNumberYear(): ?int
    {
        return $this->codeNumberYear;
    }

    public function setCodeNumberYear(int $codeNumberYear): self
    {
        $this->codeNumberYear = $codeNumberYear;

        return $this;
    }

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(?\DateTimeInterface $transactionDate): self
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    public function getLogDate(): ?\DateTimeInterface
    {
        return $this->logDate;
    }

    public function setLogDate(?\DateTimeInterface $logDate): self
    {
        $this->logDate = $logDate;

        return $this;
    }

    public function getLogTime(): ?\DateTimeInterface
    {
        return $this->logTime;
    }

    public function setLogTime(?\DateTimeInterface $logTime): self
    {
        $this->logTime = $logTime;

        return $this;
    }

    public function getEntityName(): ?string
    {
        return $this->entityName;
    }

    public function setEntityName(string $entityName): self
    {
        $this->entityName = $entityName;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getNewData(): array
    {
        return $this->newData;
    }

    public function setNewData(?array $newData): self
    {
        $this->newData = $newData;

        return $this;
    }
}
