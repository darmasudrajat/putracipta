<?php

namespace App\Entity\Stock;

use App\Entity\Master\Material;
use App\Entity\Master\Paper;
use App\Entity\Master\Product;
use App\Entity\Master\Warehouse;
use App\Repository\Stock\InventoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
#[ORM\Table(name: 'stock_inventory')]
class Inventory
{
    public const MONTH_ROMAN_NUMERALS = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(length: 20)]
    private ?string $transactionType = '';

    #[ORM\Column(length: 100)]
    private ?string $transactionSubject = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $purchasePrice = '0.00';

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\Column(length: 20)]
    private ?string $inventoryMode = '';

    #[ORM\Column]
    private ?int $transactionCodeNumberOrdinal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $transactionCodeNumberMonth = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $transactionCodeNumberYear = 0;

    #[ORM\Column]
    private ?bool $isReversed = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdInventoryDateTime = null;

    #[ORM\ManyToOne]
    private ?Warehouse $warehouse = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $quantityIn = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $quantityOut = '0.00';

    public function getCodeNumber(): string
    {
        $numerals = self::MONTH_ROMAN_NUMERALS;

        return sprintf('%04d/%s/%s/%02d', intval($this->transactionCodeNumberOrdinal), $this->getTransactionType(), $numerals[intval($this->transactionCodeNumberMonth)], intval($this->transactionCodeNumberYear));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPurchasePrice(): ?string
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(string $purchasePrice): self
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

        return $this;
    }

    public function getInventoryMode(): ?string
    {
        return $this->inventoryMode;
    }

    public function setInventoryMode(string $inventoryMode): self
    {
        $this->inventoryMode = $inventoryMode;

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

    public function isIsReversed(): ?bool
    {
        return $this->isReversed;
    }

    public function setIsReversed(bool $isReversed): self
    {
        $this->isReversed = $isReversed;

        return $this;
    }

    public function getCreatedInventoryDateTime(): ?\DateTimeInterface
    {
        return $this->createdInventoryDateTime;
    }

    public function setCreatedInventoryDateTime(?\DateTimeInterface $createdInventoryDateTime): self
    {
        $this->createdInventoryDateTime = $createdInventoryDateTime;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getQuantityIn(): ?string
    {
        return $this->quantityIn;
    }

    public function setQuantityIn(string $quantityIn): self
    {
        $this->quantityIn = $quantityIn;

        return $this;
    }

    public function getQuantityOut(): ?string
    {
        return $this->quantityOut;
    }

    public function setQuantityOut(string $quantityOut): self
    {
        $this->quantityOut = $quantityOut;

        return $this;
    }
}
