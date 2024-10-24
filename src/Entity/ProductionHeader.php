<?php

namespace App\Entity;

use App\Entity\Admin\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class ProductionHeader
{
    public const MONTH_ROMAN_NUMERALS = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    #[ORM\Column]
    #[Assert\NotNull]
    protected ?bool $isCanceled = false;

    #[ORM\Column]
    #[Assert\NotNull]
    protected ?int $codeNumberOrdinal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    protected ?int $codeNumberMonth = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    protected ?int $codeNumberYear = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\NotNull]
    protected ?\DateTimeInterface $createdTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $modifiedTransactionDateTime = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    protected ?User $createdTransactionUser = null;

    #[ORM\ManyToOne]
    protected ?User $modifiedTransactionUser = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull]
    protected ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    protected ?string $note = '';

    #[ORM\Column(type: Types::SMALLINT)]
    protected ?int $codeNumberVersion = 0;

    public abstract function getCodeNumberConstant(): string;

    public function getCodeNumber(): string
    {
        $numerals = self::MONTH_ROMAN_NUMERALS;

        return sprintf('%04d/%s/%s/%02d', intval($this->codeNumberOrdinal), $this->getCodeNumberConstant(), $numerals[intval($this->codeNumberMonth)], intval($this->codeNumberYear));
    }

    public function getCodeNumberWithVersion(): string
    {
        return $this->getCodeNumber() . '-' . $this->codeNumberVersion;
    }

    public function setCodeNumber($codeNumber): self
    {
        $nums = array_flip(self::MONTH_ROMAN_NUMERALS);

        list($ordinal, , $month, $year) = explode('/', $codeNumber);

        $this->codeNumberOrdinal = intval($ordinal);
        $this->codeNumberMonth = $nums[$month];
        $this->codeNumberYear = intval($year);

        return $this;
    }

    public function setCodeNumberToNext($codeNumber, $currentYear, $currentMonth): self
    {
        $this->setCodeNumber($codeNumber);

        $cnMonth = intval($currentMonth);
        $cnYear = intval($currentYear);
        $ordinal = $this->codeNumberOrdinal;
        if ($cnYear > $this->codeNumberYear) {
            $ordinal = 0;
        }

        $this->codeNumberOrdinal = $ordinal + 1;
        $this->codeNumberMonth = $cnMonth;
        $this->codeNumberYear = $cnYear;

        return $this;
    }

    public function isIsCanceled(): ?bool
    {
        return $this->isCanceled;
    }

    public function setIsCanceled(bool $isCanceled): self
    {
        $this->isCanceled = $isCanceled;

        return $this;
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

    public function getCreatedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->createdTransactionDateTime;
    }

    public function setCreatedTransactionDateTime(?\DateTimeInterface $createdTransactionDateTime): self
    {
        $this->createdTransactionDateTime = $createdTransactionDateTime;

        return $this;
    }

    public function getModifiedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->modifiedTransactionDateTime;
    }

    public function setModifiedTransactionDateTime(?\DateTimeInterface $modifiedTransactionDateTime): self
    {
        $this->modifiedTransactionDateTime = $modifiedTransactionDateTime;

        return $this;
    }

    public function getCreatedTransactionUser(): ?User
    {
        return $this->createdTransactionUser;
    }

    public function setCreatedTransactionUser(?User $createdTransactionUser): self
    {
        $this->createdTransactionUser = $createdTransactionUser;

        return $this;
    }

    public function getModifiedTransactionUser(): ?User
    {
        return $this->modifiedTransactionUser;
    }

    public function setModifiedTransactionUser(?User $modifiedTransactionUser): self
    {
        $this->modifiedTransactionUser = $modifiedTransactionUser;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
