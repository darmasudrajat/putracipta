<?php

namespace App\Entity\Stock;

use App\Entity\Master\Division;
use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use App\Repository\Stock\InventoryRequestHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InventoryRequestHeaderRepository::class)]
#[ORM\Table(name: 'stock_inventory_request_header')]
class InventoryRequestHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'IRQ';
    public const REQUEST_MODE_MATERIAL = 'material';
    public const REQUEST_MODE_PAPER = 'paper';
    public const REQUEST_STATUS_OPEN = 'open';
    public const REQUEST_STATUS_PARTIAL = 'partial_release';
    public const REQUEST_STATUS_CLOSE = 'close';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $partNumber = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $pickupDate = null;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestHeader', targetEntity: InventoryRequestPaperDetail::class)]
    private Collection $inventoryRequestPaperDetails;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestHeader', targetEntity: InventoryRequestMaterialDetail::class)]
    private Collection $inventoryRequestMaterialDetails;

    #[ORM\Column(length: 20)]
    private ?string $requestMode = self::REQUEST_MODE_MATERIAL;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityRelease = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityRemaining = '0.00';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Division $division = null;

    #[ORM\Column]
    private ?bool $isRead = false;

    #[ORM\Column(length: 200)]
    private ?string $inventoryRequestProductList = '';

    #[ORM\Column(length: 20)]
    private ?string $requestStatus = self::REQUEST_STATUS_OPEN;

    public function __construct()
    {
        $this->inventoryRequestPaperDetails = new ArrayCollection();
        $this->inventoryRequestMaterialDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): string
    {
        $details = [];
        if ($this->requestMode === self::REQUEST_MODE_MATERIAL) {
            $details = $this->inventoryRequestMaterialDetails;
        } else if ($this->requestMode === self::REQUEST_MODE_PAPER) {
            $details = $this->inventoryRequestPaperDetails;
        }
        $totalQuantity = 0;
        foreach ($details as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getSyncTotalQuantityRelease(): string
    {
        $details = [];
        if ($this->requestMode === self::REQUEST_MODE_MATERIAL) {
            $details = $this->inventoryRequestMaterialDetails;
        } else if ($this->requestMode === self::REQUEST_MODE_PAPER) {
            $details = $this->inventoryRequestPaperDetails;
        }
        $totalQuantity = 0;
        foreach ($details as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantityRelease();
            }
        }
        return $totalQuantity;
    }

    public function getSyncTotalQuantityRemaining(): string
    {
        $details = [];
        if ($this->requestMode === self::REQUEST_MODE_MATERIAL) {
            $details = $this->inventoryRequestMaterialDetails;
        } else if ($this->requestMode === self::REQUEST_MODE_PAPER) {
            $details = $this->inventoryRequestPaperDetails;
        }
        $totalQuantity = 0;
        foreach ($details as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantityRemaining();
            }
        }
        return $totalQuantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartNumber(): ?string
    {
        return $this->partNumber;
    }

    public function setPartNumber(string $partNumber): self
    {
        $this->partNumber = $partNumber;

        return $this;
    }

    public function getPickupDate(): ?\DateTimeInterface
    {
        return $this->pickupDate;
    }

    public function setPickupDate(?\DateTimeInterface $pickupDate): self
    {
        $this->pickupDate = $pickupDate;

        return $this;
    }

    /**
     * @return Collection<int, InventoryRequestPaperDetail>
     */
    public function getInventoryRequestPaperDetails(): Collection
    {
        return $this->inventoryRequestPaperDetails;
    }

    public function addInventoryRequestPaperDetail(InventoryRequestPaperDetail $inventoryRequestPaperDetail): self
    {
        if (!$this->inventoryRequestPaperDetails->contains($inventoryRequestPaperDetail)) {
            $this->inventoryRequestPaperDetails->add($inventoryRequestPaperDetail);
            $inventoryRequestPaperDetail->setInventoryRequestHeader($this);
        }

        return $this;
    }

    public function removeInventoryRequestPaperDetail(InventoryRequestPaperDetail $inventoryRequestPaperDetail): self
    {
        if ($this->inventoryRequestPaperDetails->removeElement($inventoryRequestPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryRequestPaperDetail->getInventoryRequestHeader() === $this) {
                $inventoryRequestPaperDetail->setInventoryRequestHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InventoryRequestMaterialDetail>
     */
    public function getInventoryRequestMaterialDetails(): Collection
    {
        return $this->inventoryRequestMaterialDetails;
    }

    public function addInventoryRequestMaterialDetail(InventoryRequestMaterialDetail $inventoryRequestMaterialDetail): self
    {
        if (!$this->inventoryRequestMaterialDetails->contains($inventoryRequestMaterialDetail)) {
            $this->inventoryRequestMaterialDetails->add($inventoryRequestMaterialDetail);
            $inventoryRequestMaterialDetail->setInventoryRequestHeader($this);
        }

        return $this;
    }

    public function removeInventoryRequestMaterialDetail(InventoryRequestMaterialDetail $inventoryRequestMaterialDetail): self
    {
        if ($this->inventoryRequestMaterialDetails->removeElement($inventoryRequestMaterialDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryRequestMaterialDetail->getInventoryRequestHeader() === $this) {
                $inventoryRequestMaterialDetail->setInventoryRequestHeader(null);
            }
        }

        return $this;
    }

    public function getRequestMode(): ?string
    {
        return $this->requestMode;
    }

    public function setRequestMode(string $requestMode): self
    {
        $this->requestMode = $requestMode;

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

    public function getTotalQuantity(): ?string
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(string $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getTotalQuantityRelease(): ?string
    {
        return $this->totalQuantityRelease;
    }

    public function setTotalQuantityRelease(string $totalQuantityRelease): self
    {
        $this->totalQuantityRelease = $totalQuantityRelease;

        return $this;
    }

    public function getTotalQuantityRemaining(): ?string
    {
        return $this->totalQuantityRemaining;
    }

    public function setTotalQuantityRemaining(string $totalQuantityRemaining): self
    {
        $this->totalQuantityRemaining = $totalQuantityRemaining;

        return $this;
    }

    public function getDivision(): ?Division
    {
        return $this->division;
    }

    public function setDivision(?Division $division): self
    {
        $this->division = $division;

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getInventoryRequestProductList(): ?string
    {
        return $this->inventoryRequestProductList;
    }

    public function setInventoryRequestProductList(string $inventoryRequestProductList): self
    {
        $this->inventoryRequestProductList = $inventoryRequestProductList;

        return $this;
    }

    public function getRequestStatus(): ?string
    {
        return $this->requestStatus;
    }

    public function setRequestStatus(string $requestStatus): self
    {
        $this->requestStatus = $requestStatus;

        return $this;
    }
}
