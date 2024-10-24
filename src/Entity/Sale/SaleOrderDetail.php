<?php

namespace App\Entity\Sale;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\SaleDetail;
use App\Entity\Stock\InventoryProductReceiveDetail;
use App\Repository\Sale\SaleOrderDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleOrderDetailRepository::class)]
#[ORM\Table(name: 'sale_sale_order_detail')]
class SaleOrderDetail extends SaleDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 6)]
    #[Assert\NotBlank]
    #[Assert\Type('numeric')]
//    #[Assert\GreaterThan(0)]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'saleOrderDetails')]
    #[Assert\NotNull]
    private ?SaleOrderHeader $saleOrderHeader = null;

    #[ORM\OneToMany(mappedBy: 'saleOrderDetail', targetEntity: DeliveryDetail::class)]
    private Collection $deliveryDetails;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 6)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $unitPriceBeforeTax = '0.00';

    #[ORM\Column]
    private ?bool $isTransactionClosed = false;

    #[ORM\OneToMany(mappedBy: 'saleOrderDetail', targetEntity: MasterOrderProductDetail::class)]
    private Collection $masterOrderProductDetails;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $linePo = 0;

    #[ORM\OneToMany(mappedBy: 'saleOrderDetail', targetEntity: InventoryProductReceiveDetail::class)]
    private Collection $inventoryProductReceiveDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $minimumToleranceQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $maximumToleranceQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?string $quantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityStock = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityProduction = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityProductionRemaining = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityDelivery = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityReturn = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $remainingQuantityDelivery = '0.00';

    public function __construct()
    {
        $this->deliveryDetails = new ArrayCollection();
        $this->masterOrderProductDetails = new ArrayCollection();
        $this->inventoryProductReceiveDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->saleOrderHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncRemainingDelivery(): string
    {
        return $this->quantity - $this->totalQuantityDelivery + $this->totalQuantityReturn;
    }
    
    public function getSyncQuantityProduction(): string 
    {
        $total = '0.00';
        
        foreach ($this->getMasterOrderProductDetails() as $masterOrderProductDetail) {
            $total += $masterOrderProductDetail->isIsCanceled() == false ? $masterOrderProductDetail->getQuantityProduction() : '0.00';
        }
        
        return $total;
    }
    
    public function getSyncRemainingProduction(): string
    {
        return $this->quantity - $this->quantityProduction;
    }

    public function getSyncUnitPriceBeforeTax(): string
    {
        return $this->saleOrderHeader->getTaxMode() === $this->saleOrderHeader::TAX_MODE_TAX_INCLUSION ? round($this->unitPrice / (1 + $this->saleOrderHeader->getTaxPercentage() / 100), 2) : $this->unitPrice;
    }

    public function getSyncTotalQuantityReturn(): string
    {
        $total = 0;
        
        foreach ($this->getDeliveryDetails() as $deliveryDetail) {
            foreach ($deliveryDetail->getSaleReturnDetails() as $saleReturnDetail) {
                $total += $saleReturnDetail->isIsCanceled() === false ? $saleReturnDetail->getQuantity() : 0;
            }
        }
        
        return $total;
    }

    public function getSyncMinimumToleranceQuantity(): string
    {
        $customer = $this->saleOrderHeader->getCustomer();
        
        return round($this->quantity * $customer->getMinimumTolerancePercentage() / 100);
    }

    public function getSyncMaximumToleranceQuantity(): string
    {
        $customer = $this->saleOrderHeader->getCustomer();
        
        return round($this->quantity * $customer->getMaximumTolerancePercentage() / 100 * -1);
    }

    public function getTotal(): string
    {
        if ($this->saleOrderHeader === null) {
            return '0.00';
        }
        return $this->saleOrderHeader->getTaxMode() === $this->saleOrderHeader::TAX_MODE_TAX_INCLUSION ? round($this->quantity * $this->unitPrice / (1 + $this->saleOrderHeader->getTaxPercentage() / 100), 2) : $this->quantity * $this->unitPrice;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getSaleOrderHeader(): ?SaleOrderHeader
    {
        return $this->saleOrderHeader;
    }

    public function setSaleOrderHeader(?SaleOrderHeader $saleOrderHeader): self
    {
        $this->saleOrderHeader = $saleOrderHeader;

        return $this;
    }

    /**
     * @return Collection<int, DeliveryDetail>
     */
    public function getDeliveryDetails(): Collection
    {
        return $this->deliveryDetails;
    }

    public function addDeliveryDetail(DeliveryDetail $deliveryDetail): self
    {
        if (!$this->deliveryDetails->contains($deliveryDetail)) {
            $this->deliveryDetails->add($deliveryDetail);
            $deliveryDetail->setSaleOrderDetail($this);
        }

        return $this;
    }

    public function removeDeliveryDetail(DeliveryDetail $deliveryDetail): self
    {
        if ($this->deliveryDetails->removeElement($deliveryDetail)) {
            // set the owning side to null (unless already changed)
            if ($deliveryDetail->getSaleOrderDetail() === $this) {
                $deliveryDetail->setSaleOrderDetail(null);
            }
        }

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getUnitPriceBeforeTax(): ?string
    {
        return $this->unitPriceBeforeTax;
    }

    public function setUnitPriceBeforeTax(string $unitPriceBeforeTax): self
    {
        $this->unitPriceBeforeTax = $unitPriceBeforeTax;

        return $this;
    }

    public function isIsTransactionClosed(): ?bool
    {
        return $this->isTransactionClosed;
    }

    public function setIsTransactionClosed(bool $isTransactionClosed): self
    {
        $this->isTransactionClosed = $isTransactionClosed;

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderProductDetail>
     */
    public function getMasterOrderProductDetails(): Collection
    {
        return $this->masterOrderProductDetails;
    }

    public function addMasterOrderProductDetail(MasterOrderProductDetail $masterOrderProductDetail): self
    {
        if (!$this->masterOrderProductDetails->contains($masterOrderProductDetail)) {
            $this->masterOrderProductDetails->add($masterOrderProductDetail);
            $masterOrderProductDetail->setSaleOrderDetail($this);
        }

        return $this;
    }

    public function removeMasterOrderProductDetail(MasterOrderProductDetail $masterOrderProductDetail): self
    {
        if ($this->masterOrderProductDetails->removeElement($masterOrderProductDetail)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderProductDetail->getSaleOrderDetail() === $this) {
                $masterOrderProductDetail->setSaleOrderDetail(null);
            }
        }

        return $this;
    }

    public function getLinePo(): ?int
    {
        return $this->linePo;
    }

    public function setLinePo(int $linePo): self
    {
        $this->linePo = $linePo;

        return $this;
    }

    /**
     * @return Collection<int, InventoryProductReceiveDetail>
     */
    public function getInventoryProductReceiveDetails(): Collection
    {
        return $this->inventoryProductReceiveDetails;
    }

    public function addInventoryProductReceiveDetail(InventoryProductReceiveDetail $inventoryProductReceiveDetail): self
    {
        if (!$this->inventoryProductReceiveDetails->contains($inventoryProductReceiveDetail)) {
            $this->inventoryProductReceiveDetails->add($inventoryProductReceiveDetail);
            $inventoryProductReceiveDetail->setSaleDetail($this);
        }

        return $this;
    }

    public function removeInventoryProductReceiveDetail(InventoryProductReceiveDetail $inventoryProductReceiveDetail): self
    {
        if ($this->inventoryProductReceiveDetails->removeElement($inventoryProductReceiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryProductReceiveDetail->getSaleDetail() === $this) {
                $inventoryProductReceiveDetail->setSaleDetail(null);
            }
        }

        return $this;
    }

    public function getMinimumToleranceQuantity(): ?string
    {
        return $this->minimumToleranceQuantity;
    }

    public function setMinimumToleranceQuantity(string $minimumToleranceQuantity): self
    {
        $this->minimumToleranceQuantity = $minimumToleranceQuantity;

        return $this;
    }

    public function getMaximumToleranceQuantity(): ?string
    {
        return $this->maximumToleranceQuantity;
    }

    public function setMaximumToleranceQuantity(string $maximumToleranceQuantity): self
    {
        $this->maximumToleranceQuantity = $maximumToleranceQuantity;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityStock(): ?string
    {
        return $this->quantityStock;
    }

    public function setQuantityStock(string $quantityStock): self
    {
        $this->quantityStock = $quantityStock;

        return $this;
    }

    public function getQuantityProduction(): ?string
    {
        return $this->quantityProduction;
    }

    public function setQuantityProduction(string $quantityProduction): self
    {
        $this->quantityProduction = $quantityProduction;

        return $this;
    }

    public function getQuantityProductionRemaining(): ?string
    {
        return $this->quantityProductionRemaining;
    }

    public function setQuantityProductionRemaining(string $quantityProductionRemaining): self
    {
        $this->quantityProductionRemaining = $quantityProductionRemaining;

        return $this;
    }

    public function getTotalQuantityDelivery(): ?string
    {
        return $this->totalQuantityDelivery;
    }

    public function setTotalQuantityDelivery(string $totalQuantityDelivery): self
    {
        $this->totalQuantityDelivery = $totalQuantityDelivery;

        return $this;
    }

    public function getTotalQuantityReturn(): ?string
    {
        return $this->totalQuantityReturn;
    }

    public function setTotalQuantityReturn(string $totalQuantityReturn): self
    {
        $this->totalQuantityReturn = $totalQuantityReturn;

        return $this;
    }

    public function getRemainingQuantityDelivery(): ?string
    {
        return $this->remainingQuantityDelivery;
    }

    public function setRemainingQuantityDelivery(string $remainingQuantityDelivery): self
    {
        $this->remainingQuantityDelivery = $remainingQuantityDelivery;

        return $this;
    }
}
