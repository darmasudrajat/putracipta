<?php

namespace App\Entity\Stock;

use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\StockDetail;
use App\Repository\Stock\InventoryRequestPaperDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InventoryRequestPaperDetailRepository::class)]
#[ORM\Table(name: 'stock_inventory_request_paper_detail')]
class InventoryRequestPaperDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryRequestPaperDetails')]
    #[Assert\NotNull]
    private ?InventoryRequestHeader $inventoryRequestHeader = null;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestPaperDetail', targetEntity: InventoryReleasePaperDetail::class)]
    private Collection $inventoryReleasePaperDetails;

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

    #[ORM\ManyToOne(inversedBy: 'inventoryRequestPaperDetails')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestPaperDetail', targetEntity: PurchaseRequestPaperDetail::class)]
    private Collection $purchaseRequestPaperDetails;

    public function __construct()
    {
        $this->inventoryReleasePaperDetails = new ArrayCollection();
        $this->purchaseRequestPaperDetails = new ArrayCollection();
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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

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
     * @return Collection<int, InventoryReleasePaperDetail>
     */
    public function getInventoryReleasePaperDetails(): Collection
    {
        return $this->inventoryReleasePaperDetails;
    }

    public function addInventoryReleasePaperDetail(InventoryReleasePaperDetail $inventoryReleasePaperDetail): self
    {
        if (!$this->inventoryReleasePaperDetails->contains($inventoryReleasePaperDetail)) {
            $this->inventoryReleasePaperDetails->add($inventoryReleasePaperDetail);
            $inventoryReleasePaperDetail->setInventoryRequestPaperDetail($this);
        }

        return $this;
    }

    public function removeInventoryReleasePaperDetail(InventoryReleasePaperDetail $inventoryReleasePaperDetail): self
    {
        if ($this->inventoryReleasePaperDetails->removeElement($inventoryReleasePaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryReleasePaperDetail->getInventoryRequestPaperDetail() === $this) {
                $inventoryReleasePaperDetail->setInventoryRequestPaperDetail(null);
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

    public function getMasterOrderHeader(): ?MasterOrderHeader
    {
        return $this->masterOrderHeader;
    }

    public function setMasterOrderHeader(?MasterOrderHeader $masterOrderHeader): self
    {
        $this->masterOrderHeader = $masterOrderHeader;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseRequestPaperDetail>
     */
    public function getPurchaseRequestPaperDetails(): Collection
    {
        return $this->purchaseRequestPaperDetails;
    }

    public function addPurchaseRequestPaperDetail(PurchaseRequestPaperDetail $purchaseRequestPaperDetail): self
    {
        if (!$this->purchaseRequestPaperDetails->contains($purchaseRequestPaperDetail)) {
            $this->purchaseRequestPaperDetails->add($purchaseRequestPaperDetail);
            $purchaseRequestPaperDetail->setInventoryRequestPaperDetail($this);
        }

        return $this;
    }

    public function removePurchaseRequestPaperDetail(PurchaseRequestPaperDetail $purchaseRequestPaperDetail): self
    {
        if ($this->purchaseRequestPaperDetails->removeElement($purchaseRequestPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseRequestPaperDetail->getInventoryRequestPaperDetail() === $this) {
                $purchaseRequestPaperDetail->setInventoryRequestPaperDetail(null);
            }
        }

        return $this;
    }
}
