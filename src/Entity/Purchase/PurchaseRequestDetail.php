<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\PurchaseDetail;
use App\Entity\Stock\InventoryRequestMaterialDetail;
use App\Repository\Purchase\PurchaseRequestDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRequestDetailRepository::class)]
#[ORM\Table(name: 'purchase_purchase_request_detail')]
class PurchaseRequestDetail extends PurchaseDetail
{
    public const TRANSACTION_STATUS_OPEN = 'open';
    public const TRANSACTION_STATUS_PURCHASE = 'purchase';
    public const TRANSACTION_STATUS_RECEIVE = 'part_rcv';
    public const TRANSACTION_STATUS_CLOSE = 'full_rcv';
    public const TRANSACTION_STATUS_CANCEL = 'cancel';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseRequestDetails')]
    #[Assert\NotNull]
    private ?PurchaseRequestHeader $purchaseRequestHeader = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $usageDate = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $memo = '';

    #[ORM\Column(length: 60)]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_OPEN;

    #[ORM\OneToMany(mappedBy: 'purchaseRequestDetail', targetEntity: PurchaseOrderDetail::class)]
    private Collection $purchaseOrderDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    #[ORM\ManyToOne(inversedBy: 'purchaseRequestDetails')]
    private ?InventoryRequestMaterialDetail $inventoryRequestMaterialDetail = null;

    public function __construct()
    {
        $this->purchaseOrderDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseRequestHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPurchaseRequestHeader(): ?PurchaseRequestHeader
    {
        return $this->purchaseRequestHeader;
    }

    public function setPurchaseRequestHeader(?PurchaseRequestHeader $purchaseRequestHeader): self
    {
        $this->purchaseRequestHeader = $purchaseRequestHeader;

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

    public function getUsageDate(): ?\DateTimeInterface
    {
        return $this->usageDate;
    }

    public function setUsageDate(?\DateTimeInterface $usageDate): self
    {
        $this->usageDate = $usageDate;

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

    public function getTransactionStatus(): ?string
    {
        return $this->transactionStatus;
    }

    public function setTransactionStatus(string $transactionStatus): self
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseOrderDetail>
     */
    public function getPurchaseOrderDetails(): Collection
    {
        return $this->purchaseOrderDetails;
    }

    public function addPurchaseOrderDetail(PurchaseOrderDetail $purchaseOrderDetail): self
    {
        if (!$this->purchaseOrderDetails->contains($purchaseOrderDetail)) {
            $this->purchaseOrderDetails->add($purchaseOrderDetail);
            $purchaseOrderDetail->setPurchaseRequestDetail($this);
        }

        return $this;
    }

    public function removePurchaseOrderDetail(PurchaseOrderDetail $purchaseOrderDetail): self
    {
        if ($this->purchaseOrderDetails->removeElement($purchaseOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseOrderDetail->getPurchaseRequestDetail() === $this) {
                $purchaseOrderDetail->setPurchaseRequestDetail(null);
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

    public function getInventoryRequestMaterialDetail(): ?InventoryRequestMaterialDetail
    {
        return $this->inventoryRequestMaterialDetail;
    }

    public function setInventoryRequestMaterialDetail(?InventoryRequestMaterialDetail $inventoryRequestMaterialDetail): self
    {
        $this->inventoryRequestMaterialDetail = $inventoryRequestMaterialDetail;

        return $this;
    }
}
