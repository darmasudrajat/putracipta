<?php

namespace App\Entity\Stock;

use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use App\Entity\Production\MasterOrderHeader;
use App\Repository\Stock\InventoryProductReceiveHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InventoryProductReceiveHeaderRepository::class)]
#[ORM\Table(name: 'stock_inventory_product_receive_header')]
class InventoryProductReceiveHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'FGR';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'inventoryProductReceiveHeader', targetEntity: InventoryProductReceiveDetail::class)]
    private Collection $inventoryProductReceiveDetails;

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveHeaders')]
    #[Assert\NotNull]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\Column(length: 200)]
    private ?string $productDetailLists = '';

    #[ORM\Column(length: 200)]
    private ?string $productCodeLists = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantity = '0.00';

    public function __construct()
    {
        $this->inventoryProductReceiveDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->inventoryProductReceiveDetails as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantityTotalPieces();
            }
        }
        return $totalQuantity;
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
            $inventoryProductReceiveDetail->setInventoryProductReceiveHeader($this);
        }

        return $this;
    }

    public function removeInventoryProductReceiveDetail(InventoryProductReceiveDetail $inventoryProductReceiveDetail): self
    {
        if ($this->inventoryProductReceiveDetails->removeElement($inventoryProductReceiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryProductReceiveDetail->getInventoryProductReceiveHeader() === $this) {
                $inventoryProductReceiveDetail->setInventoryProductReceiveHeader(null);
            }
        }

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

    public function getProductDetailLists(): ?string
    {
        return $this->productDetailLists;
    }

    public function setProductDetailLists(string $productDetailLists): self
    {
        $this->productDetailLists = $productDetailLists;

        return $this;
    }

    public function getProductCodeLists(): ?string
    {
        return $this->productCodeLists;
    }

    public function setProductCodeLists(string $productCodeLists): self
    {
        $this->productCodeLists = $productCodeLists;

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
