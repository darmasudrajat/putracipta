<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\PurchaseDetail;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseOrderDetailRepository::class)]
#[ORM\Table(name: 'purchase_purchase_order_detail')]
#[UniqueEntity('purchaseRequestDetail')]
class PurchaseOrderDetail extends PurchaseDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseOrderDetails')]
    #[Assert\NotNull]
    private ?PurchaseOrderHeader $purchaseOrderHeader = null;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderDetail', targetEntity: ReceiveDetail::class)]
    private Collection $receiveDetails;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Unit $unit = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $unitPriceBeforeTax = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $total = '0.00';

    #[ORM\Column]
    private ?bool $isTransactionClosed = false;

    #[ORM\ManyToOne(inversedBy: 'purchaseOrderDetails')]
    private ?PurchaseRequestDetail $purchaseRequestDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $totalReceive = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $totalReturn = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $remainingReceive = '0.00';

    public function __construct()
    {
        $this->receiveDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseOrderHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncRemainingReceive(): int
    {
        return $this->quantity - $this->totalReceive + $this->totalReturn;
    }

    public function getSyncUnitPriceBeforeTax(): string
    {
        return $this->purchaseOrderHeader->getTaxMode() === $this->purchaseOrderHeader::TAX_MODE_TAX_INCLUSION ? round($this->unitPrice / (1 + $this->purchaseOrderHeader->getTaxPercentage() / 100), 2) : $this->unitPrice;
    }

    public function getSyncTotal(): string
    {
        return $this->quantity * $this->getUnitPriceBeforeTax();
    }

    public function getSyncIsTransactionClosed(): bool
    {
        $isClosed = $this->purchaseOrderHeader->getTransactionStatus() === $this->purchaseOrderHeader::TRANSACTION_STATUS_FULL_RECEIVE ? true : $this->isTransactionClosed;
        return $isClosed;
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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getPurchaseOrderHeader(): ?PurchaseOrderHeader
    {
        return $this->purchaseOrderHeader;
    }

    public function setPurchaseOrderHeader(?PurchaseOrderHeader $purchaseOrderHeader): self
    {
        $this->purchaseOrderHeader = $purchaseOrderHeader;

        return $this;
    }

    /**
     * @return Collection<int, ReceiveDetail>
     */
    public function getReceiveDetails(): Collection
    {
        return $this->receiveDetails;
    }

    public function addReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if (!$this->receiveDetails->contains($receiveDetail)) {
            $this->receiveDetails->add($receiveDetail);
            $receiveDetail->setPurchaseOrderDetail($this);
        }

        return $this;
    }

    public function removeReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if ($this->receiveDetails->removeElement($receiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($receiveDetail->getPurchaseOrderDetail() === $this) {
                $receiveDetail->setPurchaseOrderDetail(null);
            }
        }

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

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

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

    public function getPurchaseRequestDetail(): ?PurchaseRequestDetail
    {
        return $this->purchaseRequestDetail;
    }

    public function setPurchaseRequestDetail(?PurchaseRequestDetail $purchaseRequestDetail): self
    {
        $this->purchaseRequestDetail = $purchaseRequestDetail;

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

    public function getTotalReceive(): ?string
    {
        return $this->totalReceive;
    }

    public function setTotalReceive(string $totalReceive): self
    {
        $this->totalReceive = $totalReceive;

        return $this;
    }

    public function getTotalReturn(): ?string
    {
        return $this->totalReturn;
    }

    public function setTotalReturn(string $totalReturn): self
    {
        $this->totalReturn = $totalReturn;

        return $this;
    }

    public function getRemainingReceive(): ?string
    {
        return $this->remainingReceive;
    }

    public function setRemainingReceive(string $remainingReceive): self
    {
        $this->remainingReceive = $remainingReceive;

        return $this;
    }
}
