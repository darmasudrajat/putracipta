<?php

namespace App\Entity\Stock;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\Purchase\PurchaseRequestDetail;
use App\Entity\StockDetail;
use App\Repository\Stock\InventoryRequestMaterialDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InventoryRequestMaterialDetailRepository::class)]
#[ORM\Table(name: 'stock_inventory_request_material_detail')]
class InventoryRequestMaterialDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryRequestMaterialDetails')]
    #[Assert\NotNull]
    private ?InventoryRequestHeader $inventoryRequestHeader = null;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestMaterialDetail', targetEntity: InventoryReleaseMaterialDetail::class)]
    private Collection $inventoryReleaseMaterialDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRelease = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRemaining = '0.00';

    #[ORM\OneToMany(mappedBy: 'inventoryRequestMaterialDetail', targetEntity: PurchaseRequestDetail::class)]
    private Collection $purchaseRequestDetails;

    public function __construct()
    {
        $this->inventoryReleaseMaterialDetails = new ArrayCollection();
        $this->purchaseRequestDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->inventoryRequestHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityRemaining(): string
    {
        return $this->quantity - $this->quantityRelease;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

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

    public function getInventoryRequestHeader(): ?InventoryRequestHeader
    {
        return $this->inventoryRequestHeader;
    }

    public function setInventoryRequestHeader(?InventoryRequestHeader $inventoryRequestHeader): self
    {
        $this->inventoryRequestHeader = $inventoryRequestHeader;

        return $this;
    }

    /**
     * @return Collection<int, InventoryReleaseMaterialDetail>
     */
    public function getInventoryReleaseMaterialDetails(): Collection
    {
        return $this->inventoryReleaseMaterialDetails;
    }

    public function addInventoryReleaseMaterialDetail(InventoryReleaseMaterialDetail $inventoryReleaseMaterialDetail): self
    {
        if (!$this->inventoryReleaseMaterialDetails->contains($inventoryReleaseMaterialDetail)) {
            $this->inventoryReleaseMaterialDetails->add($inventoryReleaseMaterialDetail);
            $inventoryReleaseMaterialDetail->setInventoryRequestMaterialDetail($this);
        }

        return $this;
    }

    public function removeInventoryReleaseMaterialDetail(InventoryReleaseMaterialDetail $inventoryReleaseMaterialDetail): self
    {
        if ($this->inventoryReleaseMaterialDetails->removeElement($inventoryReleaseMaterialDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryReleaseMaterialDetail->getInventoryRequestMaterialDetail() === $this) {
                $inventoryReleaseMaterialDetail->setInventoryRequestMaterialDetail(null);
            }
        }

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

    public function getQuantityRelease(): ?string
    {
        return $this->quantityRelease;
    }

    public function setQuantityRelease(string $quantityRelease): self
    {
        $this->quantityRelease = $quantityRelease;

        return $this;
    }

    public function getQuantityRemaining(): ?string
    {
        return $this->quantityRemaining;
    }

    public function setQuantityRemaining(string $quantityRemaining): self
    {
        $this->quantityRemaining = $quantityRemaining;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseRequestDetail>
     */
    public function getPurchaseRequestDetails(): Collection
    {
        return $this->purchaseRequestDetails;
    }

    public function addPurchaseRequestDetail(PurchaseRequestDetail $purchaseRequestDetail): self
    {
        if (!$this->purchaseRequestDetails->contains($purchaseRequestDetail)) {
            $this->purchaseRequestDetails->add($purchaseRequestDetail);
            $purchaseRequestDetail->setInventoryRequestMaterialDetail($this);
        }

        return $this;
    }

    public function removePurchaseRequestDetail(PurchaseRequestDetail $purchaseRequestDetail): self
    {
        if ($this->purchaseRequestDetails->removeElement($purchaseRequestDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseRequestDetail->getInventoryRequestMaterialDetail() === $this) {
                $purchaseRequestDetail->setInventoryRequestMaterialDetail(null);
            }
        }

        return $this;
    }
}
