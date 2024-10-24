<?php

namespace App\Entity\Stock;

use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdjustmentStockHeaderRepository::class)]
#[ORM\Table(name: 'stock_adjustment_stock_header')]
class AdjustmentStockHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'AJS';
    public const ADJUSTMENT_MODE_MATERIAL = 'material';
    public const ADJUSTMENT_MODE_PAPER = 'paper';
    public const ADJUSTMENT_MODE_PRODUCT = 'product';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\Column(length: 20)]
    private ?string $adjustmentMode = '';

    #[ORM\OneToMany(mappedBy: 'adjustmentStockHeader', targetEntity: AdjustmentStockMaterialDetail::class)]
    private Collection $adjustmentStockMaterialDetails;

    #[ORM\OneToMany(mappedBy: 'adjustmentStockHeader', targetEntity: AdjustmentStockPaperDetail::class)]
    private Collection $adjustmentStockPaperDetails;

    #[ORM\OneToMany(mappedBy: 'adjustmentStockHeader', targetEntity: AdjustmentStockProductDetail::class)]
    private Collection $adjustmentStockProductDetails;

    #[ORM\Column(length: 200)]
    private ?string $adjustmentStockItemList = '';

    #[ORM\Column(length: 200)]
    private ?string $adjustmentStockItemCodeList = '';

    public function __construct()
    {
        $this->adjustmentStockMaterialDetails = new ArrayCollection();
        $this->adjustmentStockPaperDetails = new ArrayCollection();
        $this->adjustmentStockProductDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function removeAdjustmentStockDetail(AdjustmentStockDetail $adjustmentStockDetail): self
    {
        if ($this->adjustmentStockDetails->removeElement($adjustmentStockDetail)) {
            // set the owning side to null (unless already changed)
            if ($adjustmentStockDetail->getAdjustmentStockHeader() === $this) {
                $adjustmentStockDetail->setAdjustmentStockHeader(null);
            }
        }

        return $this;
    }

    public function getAdjustmentMode(): ?string
    {
        return $this->adjustmentMode;
    }

    public function setAdjustmentMode(string $adjustmentMode): self
    {
        $this->adjustmentMode = $adjustmentMode;

        return $this;
    }

    /**
     * @return Collection<int, AdjustmentStockMaterialDetail>
     */
    public function getAdjustmentStockMaterialDetails(): Collection
    {
        return $this->adjustmentStockMaterialDetails;
    }

    public function addAdjustmentStockMaterialDetail(AdjustmentStockMaterialDetail $adjustmentStockMaterialDetail): self
    {
        if (!$this->adjustmentStockMaterialDetails->contains($adjustmentStockMaterialDetail)) {
            $this->adjustmentStockMaterialDetails->add($adjustmentStockMaterialDetail);
            $adjustmentStockMaterialDetail->setAdjustmentStockHeader($this);
        }

        return $this;
    }

    public function removeAdjustmentStockMaterialDetail(AdjustmentStockMaterialDetail $adjustmentStockMaterialDetail): self
    {
        if ($this->adjustmentStockMaterialDetails->removeElement($adjustmentStockMaterialDetail)) {
            // set the owning side to null (unless already changed)
            if ($adjustmentStockMaterialDetail->getAdjustmentStockHeader() === $this) {
                $adjustmentStockMaterialDetail->setAdjustmentStockHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AdjustmentStockPaperDetail>
     */
    public function getAdjustmentStockPaperDetails(): Collection
    {
        return $this->adjustmentStockPaperDetails;
    }

    public function addAdjustmentStockPaperDetail(AdjustmentStockPaperDetail $adjustmentStockPaperDetail): self
    {
        if (!$this->adjustmentStockPaperDetails->contains($adjustmentStockPaperDetail)) {
            $this->adjustmentStockPaperDetails->add($adjustmentStockPaperDetail);
            $adjustmentStockPaperDetail->setAdjustmentStockHeader($this);
        }

        return $this;
    }

    public function removeAdjustmentStockPaperDetail(AdjustmentStockPaperDetail $adjustmentStockPaperDetail): self
    {
        if ($this->adjustmentStockPaperDetails->removeElement($adjustmentStockPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($adjustmentStockPaperDetail->getAdjustmentStockHeader() === $this) {
                $adjustmentStockPaperDetail->setAdjustmentStockHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AdjustmentStockProductDetail>
     */
    public function getAdjustmentStockProductDetails(): Collection
    {
        return $this->adjustmentStockProductDetails;
    }

    public function addAdjustmentStockProductDetail(AdjustmentStockProductDetail $adjustmentStockProductDetail): self
    {
        if (!$this->adjustmentStockProductDetails->contains($adjustmentStockProductDetail)) {
            $this->adjustmentStockProductDetails->add($adjustmentStockProductDetail);
            $adjustmentStockProductDetail->setAdjustmentStockHeader($this);
        }

        return $this;
    }

    public function removeAdjustmentStockProductDetail(AdjustmentStockProductDetail $adjustmentStockProductDetail): self
    {
        if ($this->adjustmentStockProductDetails->removeElement($adjustmentStockProductDetail)) {
            // set the owning side to null (unless already changed)
            if ($adjustmentStockProductDetail->getAdjustmentStockHeader() === $this) {
                $adjustmentStockProductDetail->setAdjustmentStockHeader(null);
            }
        }

        return $this;
    }

    public function getAdjustmentStockItemList(): ?string
    {
        return $this->adjustmentStockItemList;
    }

    public function setAdjustmentStockItemList(string $adjustmentStockItemList): self
    {
        $this->adjustmentStockItemList = $adjustmentStockItemList;

        return $this;
    }

    public function getAdjustmentStockItemCodeList(): ?string
    {
        return $this->adjustmentStockItemCodeList;
    }

    public function setAdjustmentStockItemCodeList(string $adjustmentStockItemCodeList): self
    {
        $this->adjustmentStockItemCodeList = $adjustmentStockItemCodeList;

        return $this;
    }
}
