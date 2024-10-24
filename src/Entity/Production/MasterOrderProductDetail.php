<?php

namespace App\Entity\Production;

use App\Entity\Master\Product;
use App\Entity\Sale\DeliveryDetail;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\ProductionDetail;
use App\Entity\Stock\InventoryProductReceiveDetail;
use App\Repository\Production\MasterOrderProductDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MasterOrderProductDetailRepository::class)]
#[ORM\Table(name: 'production_master_order_product_detail')]
class MasterOrderProductDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderProductDetails')]
    #[Assert\NotNull]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderProductDetails')]
    private ?SaleOrderDetail $saleOrderDetail = null;

    #[ORM\OneToMany(mappedBy: 'masterOrderProductDetail', targetEntity: InventoryProductReceiveDetail::class)]
    private Collection $inventoryProductReceiveDetails;

    #[ORM\OneToMany(mappedBy: 'masterOrderProductDetail', targetEntity: DeliveryDetail::class)]
    private Collection $deliveryDetails;

    #[ORM\OneToMany(mappedBy: 'masterOrderProductDetail', targetEntity: QualityControlSortingDetail::class)]
    private Collection $qualityControlSortingDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityOrder = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityStock = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityShortage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityProduction = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityDelivery = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $remainingStockDelivery = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityPrinting = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityInventoryReceive = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $remainingInventoryReceive = '0.00';

    public function __construct()
    {
        $this->inventoryProductReceiveDetails = new ArrayCollection();
        $this->deliveryDetails = new ArrayCollection();
        $this->qualityControlSortingDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->masterOrderHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityShortage() 
    {
        return $this->quantityProduction - $this->quantityStock;
    }
    
    public function getSyncRemainingInventoryReceive() 
    {
        return $this->quantityShortage - $this->quantityInventoryReceive;
    }
    
    public function getSyncRemainingStockDelivery() 
    {
        $quantityStock = $this->quantityShortage > 0 ? $this->quantityInventoryReceive : $this->quantityProduction;
        return $quantityStock - $this->quantityDelivery;
    }
    
    public function getDeliveryLotNumber()
    {
        return sprintf('%04d.%02d', intval($this->getMasterOrderHeader()->getCodeNumberOrdinal()), intval($this->getMasterOrderHeader()->getCodeNumberYear()));
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getMasterOrderHeader(): ?MasterOrderHeader
    {
        return $this->masterOrderHeader;
    }

    public function setMasterOrderHeader(?MasterOrderHeader $masterOrderHeader): self
    {
        $this->masterOrderHeader = $masterOrderHeader;

        return $this;
    }

    public function getSaleOrderDetail(): ?SaleOrderDetail
    {
        return $this->saleOrderDetail;
    }

    public function setSaleOrderDetail(?SaleOrderDetail $saleOrderDetail): self
    {
        $this->saleOrderDetail = $saleOrderDetail;

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
            $inventoryProductReceiveDetail->setMasterOrderProductDetail($this);
        }

        return $this;
    }

    public function removeInventoryProductReceiveDetail(InventoryProductReceiveDetail $inventoryProductReceiveDetail): self
    {
        if ($this->inventoryProductReceiveDetails->removeElement($inventoryProductReceiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryProductReceiveDetail->getMasterOrderProductDetail() === $this) {
                $inventoryProductReceiveDetail->setMasterOrderProductDetail(null);
            }
        }

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
            $deliveryDetail->setMasterOrderProductDetail($this);
        }

        return $this;
    }

    public function removeDeliveryDetail(DeliveryDetail $deliveryDetail): self
    {
        if ($this->deliveryDetails->removeElement($deliveryDetail)) {
            // set the owning side to null (unless already changed)
            if ($deliveryDetail->getMasterOrderProductDetail() === $this) {
                $deliveryDetail->setMasterOrderProductDetail(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QualityControlSortingDetail>
     */
    public function getQualityControlSortingDetails(): Collection
    {
        return $this->qualityControlSortingDetails;
    }

    public function addQualityControlSortingDetail(QualityControlSortingDetail $qualityControlSortingDetail): self
    {
        if (!$this->qualityControlSortingDetails->contains($qualityControlSortingDetail)) {
            $this->qualityControlSortingDetails->add($qualityControlSortingDetail);
            $qualityControlSortingDetail->setMasterOrderProductDetail($this);
        }

        return $this;
    }

    public function removeQualityControlSortingDetail(QualityControlSortingDetail $qualityControlSortingDetail): self
    {
        if ($this->qualityControlSortingDetails->removeElement($qualityControlSortingDetail)) {
            // set the owning side to null (unless already changed)
            if ($qualityControlSortingDetail->getMasterOrderProductDetail() === $this) {
                $qualityControlSortingDetail->setMasterOrderProductDetail(null);
            }
        }

        return $this;
    }

    public function getQuantityOrder(): ?string
    {
        return $this->quantityOrder;
    }

    public function setQuantityOrder(string $quantityOrder): self
    {
        $this->quantityOrder = $quantityOrder;

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

    public function getQuantityShortage(): ?string
    {
        return $this->quantityShortage;
    }

    public function setQuantityShortage(string $quantityShortage): self
    {
        $this->quantityShortage = $quantityShortage;

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

    public function getQuantityDelivery(): ?string
    {
        return $this->quantityDelivery;
    }

    public function setQuantityDelivery(string $quantityDelivery): self
    {
        $this->quantityDelivery = $quantityDelivery;

        return $this;
    }

    public function getRemainingStockDelivery(): ?string
    {
        return $this->remainingStockDelivery;
    }

    public function setRemainingStockDelivery(string $remainingStockDelivery): self
    {
        $this->remainingStockDelivery = $remainingStockDelivery;

        return $this;
    }

    public function getQuantityPrinting(): ?string
    {
        return $this->quantityPrinting;
    }

    public function setQuantityPrinting(string $quantityPrinting): self
    {
        $this->quantityPrinting = $quantityPrinting;

        return $this;
    }

    public function getQuantityInventoryReceive(): ?string
    {
        return $this->quantityInventoryReceive;
    }

    public function setQuantityInventoryReceive(string $quantityInventoryReceive): self
    {
        $this->quantityInventoryReceive = $quantityInventoryReceive;

        return $this;
    }

    public function getRemainingInventoryReceive(): ?string
    {
        return $this->remainingInventoryReceive;
    }

    public function setRemainingInventoryReceive(string $remainingInventoryReceive): self
    {
        $this->remainingInventoryReceive = $remainingInventoryReceive;

        return $this;
    }
}
