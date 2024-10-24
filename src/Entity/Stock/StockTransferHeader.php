<?php

namespace App\Entity\Stock;

use App\Entity\StockHeader;
use App\Entity\Master\Warehouse;
use App\Repository\Stock\StockTransferHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StockTransferHeaderRepository::class)]
#[ORM\Table(name: 'stock_stock_transfer_header')]
class StockTransferHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'TRF';
    public const TRANSFER_MODE_MATERIAL = 'material';
    public const TRANSFER_MODE_PAPER = 'paper';
    public const TRANSFER_MODE_PRODUCT = 'product';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouseFrom = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouseTo = null;

    #[ORM\Column(length: 20)]
    private ?string $transferMode = self::TRANSFER_MODE_MATERIAL;

    #[ORM\OneToMany(mappedBy: 'stockTransferHeader', targetEntity: StockTransferMaterialDetail::class)]
    private Collection $stockTransferMaterialDetails;

    #[ORM\OneToMany(mappedBy: 'stockTransferHeader', targetEntity: StockTransferPaperDetail::class)]
    private Collection $stockTransferPaperDetails;

    #[ORM\OneToMany(mappedBy: 'stockTransferHeader', targetEntity: StockTransferProductDetail::class)]
    private Collection $stockTransferProductDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantity = '0.00';

    public function __construct()
    {
        $this->stockTransferMaterialDetails = new ArrayCollection();
        $this->stockTransferPaperDetails = new ArrayCollection();
        $this->stockTransferProductDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): string
    {
        $details = [];
        if ($this->transferMode === self::TRANSFER_MODE_MATERIAL) {
            $details = $this->stockTransferMaterialDetails;
        } else if ($this->transferMode === self::TRANSFER_MODE_PAPER) {
            $details = $this->stockTransferPaperDetails;
        } else if ($this->transferMode === self::TRANSFER_MODE_PRODUCT) {
            $details = $this->stockTransferProductDetails;
        }
        $totalQuantity = 0;
        foreach ($details as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWarehouseFrom(): ?Warehouse
    {
        return $this->warehouseFrom;
    }

    public function setWarehouseFrom(?Warehouse $warehouseFrom): self
    {
        $this->warehouseFrom = $warehouseFrom;

        return $this;
    }

    public function getWarehouseTo(): ?Warehouse
    {
        return $this->warehouseTo;
    }

    public function setWarehouseTo(?Warehouse $warehouseTo): self
    {
        $this->warehouseTo = $warehouseTo;

        return $this;
    }

    public function getTransferMode(): ?string
    {
        return $this->transferMode;
    }

    public function setTransferMode(string $transferMode): self
    {
        $this->transferMode = $transferMode;

        return $this;
    }

    /**
     * @return Collection<int, StockTransferMaterialDetail>
     */
    public function getStockTransferMaterialDetails(): Collection
    {
        return $this->stockTransferMaterialDetails;
    }

    public function addStockTransferMaterialDetail(StockTransferMaterialDetail $stockTransferMaterialDetail): self
    {
        if (!$this->stockTransferMaterialDetails->contains($stockTransferMaterialDetail)) {
            $this->stockTransferMaterialDetails->add($stockTransferMaterialDetail);
            $stockTransferMaterialDetail->setStockTransferHeader($this);
        }

        return $this;
    }

    public function removeStockTransferMaterialDetail(StockTransferMaterialDetail $stockTransferMaterialDetail): self
    {
        if ($this->stockTransferMaterialDetails->removeElement($stockTransferMaterialDetail)) {
            // set the owning side to null (unless already changed)
            if ($stockTransferMaterialDetail->getStockTransferHeader() === $this) {
                $stockTransferMaterialDetail->setStockTransferHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StockTransferPaperDetail>
     */
    public function getStockTransferPaperDetails(): Collection
    {
        return $this->stockTransferPaperDetails;
    }

    public function addStockTransferPaperDetail(StockTransferPaperDetail $stockTransferPaperDetail): self
    {
        if (!$this->stockTransferPaperDetails->contains($stockTransferPaperDetail)) {
            $this->stockTransferPaperDetails->add($stockTransferPaperDetail);
            $stockTransferPaperDetail->setStockTransferHeader($this);
        }

        return $this;
    }

    public function removeStockTransferPaperDetail(StockTransferPaperDetail $stockTransferPaperDetail): self
    {
        if ($this->stockTransferPaperDetails->removeElement($stockTransferPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($stockTransferPaperDetail->getStockTransferHeader() === $this) {
                $stockTransferPaperDetail->setStockTransferHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StockTransferProductDetail>
     */
    public function getStockTransferProductDetails(): Collection
    {
        return $this->stockTransferProductDetails;
    }

    public function addStockTransferProductDetail(StockTransferProductDetail $stockTransferProductDetail): self
    {
        if (!$this->stockTransferProductDetails->contains($stockTransferProductDetail)) {
            $this->stockTransferProductDetails->add($stockTransferProductDetail);
            $stockTransferProductDetail->setStockTransferHeader($this);
        }

        return $this;
    }

    public function removeStockTransferProductDetail(StockTransferProductDetail $stockTransferProductDetail): self
    {
        if ($this->stockTransferProductDetails->removeElement($stockTransferProductDetail)) {
            // set the owning side to null (unless already changed)
            if ($stockTransferProductDetail->getStockTransferHeader() === $this) {
                $stockTransferProductDetail->setStockTransferHeader(null);
            }
        }

        return $this;
    }

    public function getTotalQuantity(): ?string
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(string $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }
}
