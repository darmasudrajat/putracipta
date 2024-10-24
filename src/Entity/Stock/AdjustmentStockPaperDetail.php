<?php

namespace App\Entity\Stock;

use App\Entity\Master\Paper;
use App\Entity\StockDetail;
use App\Repository\Stock\AdjustmentStockPaperDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdjustmentStockPaperDetailRepository::class)]
#[ORM\Table(name: 'stock_adjustment_stock_paper_detail')]
class AdjustmentStockPaperDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne(inversedBy: 'adjustmentStockPaperDetails')]
    #[Assert\NotNull]
    private ?AdjustmentStockHeader $adjustmentStockHeader = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityCurrent = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityAdjustment = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityDifference = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->adjustmentStockHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityDifference(): string
    {
        return $this->quantityAdjustment - $this->quantityCurrent;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdjustmentStockHeader(): ?AdjustmentStockHeader
    {
        return $this->adjustmentStockHeader;
    }

    public function setAdjustmentStockHeader(?AdjustmentStockHeader $adjustmentStockHeader): self
    {
        $this->adjustmentStockHeader = $adjustmentStockHeader;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }

    public function getQuantityCurrent(): ?string
    {
        return $this->quantityCurrent;
    }

    public function setQuantityCurrent(string $quantityCurrent): self
    {
        $this->quantityCurrent = $quantityCurrent;

        return $this;
    }

    public function getQuantityAdjustment(): ?string
    {
        return $this->quantityAdjustment;
    }

    public function setQuantityAdjustment(string $quantityAdjustment): self
    {
        $this->quantityAdjustment = $quantityAdjustment;

        return $this;
    }

    public function getQuantityDifference(): ?string
    {
        return $this->quantityDifference;
    }

    public function setQuantityDifference(string $quantityDifference): self
    {
        $this->quantityDifference = $quantityDifference;

        return $this;
    }
}
