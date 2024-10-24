<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\PurchaseDetail;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseOrderPaperDetailRepository::class)]
#[ORM\Table(name: 'purchase_purchase_order_paper_detail')]
#[UniqueEntity('purchaseRequestPaperDetail')]
class PurchaseOrderPaperDetail extends PurchaseDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $apkiValue = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Type('numeric')]
    private ?string $associationPrice = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Type('numeric')]
    private ?string $weightPrice = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne(inversedBy: 'purchaseOrderPaperDetails')]
    #[Assert\NotNull]
    private ?PurchaseOrderPaperHeader $purchaseOrderPaperHeader = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderPaperDetail', targetEntity: ReceiveDetail::class)]
    private Collection $receiveDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $weight = '0.00';

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull]
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

    #[ORM\ManyToOne(inversedBy: 'purchaseOrderPaperDetails')]
    private ?PurchaseRequestPaperDetail $purchaseRequestPaperDetail = null;

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

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $length = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $width = '0.00';

    public function __construct()
    {
        $this->receiveDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseOrderPaperHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncIsTransactionClosed(): bool
    {
        $isClosed = $this->purchaseOrderPaperHeader->getTransactionStatus() === $this->purchaseOrderPaperHeader::TRANSACTION_STATUS_FULL_RECEIVE ? true : $this->isTransactionClosed;
        return $isClosed;
    }

    public function getSyncRemainingReceive(): int
    {
        return $this->quantity - $this->totalReceive + $this->totalReturn;
    }

    public function getSyncWeightPrice(): string
    {
        return (1 + $this->apkiValue / 100) * $this->associationPrice;
    }

    public function getSyncUnitPrice(): string
    {
        $weight = empty($this->paper) ? 1 : $this->paper->getWeight();
        $length = empty($this->paper) ? 1 : $this->paper->getLength();
        $width = empty($this->paper) ? 1 : $this->paper->getWidth();

        return $weight * $length * $width / 20000 * $this->getWeightPrice();
    }

    public function getSyncUnitPriceBeforeTax(): string
    {
        return $this->purchaseOrderPaperHeader->getTaxMode() === $this->purchaseOrderPaperHeader::TAX_MODE_TAX_INCLUSION ? round($this->unitPrice / (1 + $this->purchaseOrderPaperHeader->getTaxPercentage() / 100), 2) : $this->unitPrice;
    }

    public function getSyncTotal(): string
    {
        return $this->quantity * $this->getUnitPriceBeforeTax();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApkiValue(): ?int
    {
        return $this->apkiValue;
    }

    public function setApkiValue(int $apkiValue): self
    {
        $this->apkiValue = $apkiValue;

        return $this;
    }

    public function getAssociationPrice(): ?string
    {
        return $this->associationPrice;
    }

    public function setAssociationPrice(string $associationPrice): self
    {
        $this->associationPrice = $associationPrice;

        return $this;
    }

    public function getWeightPrice(): ?string
    {
        return $this->weightPrice;
    }

    public function setWeightPrice(string $weightPrice): self
    {
        $this->weightPrice = $weightPrice;

        return $this;
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

    public function getPurchaseOrderPaperHeader(): ?PurchaseOrderPaperHeader
    {
        return $this->purchaseOrderPaperHeader;
    }

    public function setPurchaseOrderPaperHeader(?PurchaseOrderPaperHeader $purchaseOrderPaperHeader): self
    {
        $this->purchaseOrderPaperHeader = $purchaseOrderPaperHeader;

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
            $receiveDetail->setPurchaseOrderPaperDetail($this);
        }

        return $this;
    }

    public function removeReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if ($this->receiveDetails->removeElement($receiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($receiveDetail->getPurchaseOrderPaperDetail() === $this) {
                $receiveDetail->setPurchaseOrderPaperDetail(null);
            }
        }

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

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

    public function getPurchaseRequestPaperDetail(): ?PurchaseRequestPaperDetail
    {
        return $this->purchaseRequestPaperDetail;
    }

    public function setPurchaseRequestPaperDetail(?PurchaseRequestPaperDetail $purchaseRequestPaperDetail): self
    {
        $this->purchaseRequestPaperDetail = $purchaseRequestPaperDetail;

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

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(string $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }
}
