<?php

namespace App\Entity\Purchase;

use App\Entity\Admin\User;
use App\Entity\Master\Currency;
use App\Entity\Master\Supplier;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\PurchaseHeader;
use App\Repository\Purchase\PurchaseOrderPaperHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseOrderPaperHeaderRepository::class)]
#[ORM\Table(name: 'purchase_purchase_order_paper_header')]
class PurchaseOrderPaperHeader extends PurchaseHeader
{
    public const CODE_NUMBER_CONSTANT = 'POP';
    public const DISCOUNT_VALUE_TYPE_PERCENTAGE = 'percentage';
    public const DISCOUNT_VALUE_TYPE_NOMINAL = 'nominal';
    public const TAX_MODE_NON_TAX = 'non_tax';
    public const TAX_MODE_TAX_EXCLUSION = 'tax_exclusion';
    public const TAX_MODE_TAX_INCLUSION = 'tax_inclusion';
    public const TRANSACTION_STATUS_DRAFT = 'draft';
    public const TRANSACTION_STATUS_HOLD = 'hold';
    public const TRANSACTION_STATUS_RELEASE = 'release';
    public const TRANSACTION_STATUS_APPROVE = 'approve';
    public const TRANSACTION_STATUS_REJECT = 'reject';
    public const TRANSACTION_STATUS_PARTIAL_RECEIVE = 'partial_receive';
    public const TRANSACTION_STATUS_FULL_RECEIVE = 'full_receive';
    public const TRANSACTION_STATUS_CANCEL = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $discountValueType = self::DISCOUNT_VALUE_TYPE_PERCENTAGE;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Type('numeric')]
    private ?string $discountValue = '0.00';

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $taxMode = self::TAX_MODE_NON_TAX;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $taxPercentage = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $taxNominal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $subTotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $grandTotal = '0.00';

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $totalRemainingReceive = 0;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_DRAFT;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvedTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $rejectedTransactionDateTime = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne]
    private ?Currency $currency = null;

    #[ORM\ManyToOne]
    private ?User $approvedTransactionUser = null;

    #[ORM\ManyToOne]
    private ?User $rejectedTransactionUser = null;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderPaperHeader', targetEntity: PurchaseOrderPaperDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $purchaseOrderPaperDetails;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderPaperHeader', targetEntity: ReceiveHeader::class)]
    private Collection $receiveHeaders;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $isOnHold = false;

    #[ORM\Column]
    private ?bool $hasReturnTransaction = false;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderPaperHeader', targetEntity: MasterOrderHeader::class)]
    private Collection $masterOrderHeaders;

    #[ORM\Column(length: 100)]
    private ?string $rejectNote = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $purchaseOrderPaperList = '';

    public function __construct()
    {
        $this->purchaseOrderPaperDetails = new ArrayCollection();
        $this->receiveHeaders = new ArrayCollection();
        $this->masterOrderHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTaxNominal(): string
    {
        $taxNominal = $this->getSubTotalAfterDiscount() * $this->taxPercentage / 100;
        return $taxNominal;
    }

    public function getSyncSubTotal(): string
    {
        $subTotal = '0.00';
        foreach ($this->purchaseOrderPaperDetails as $purchaseOrderPaperDetail) {
            if (!$purchaseOrderPaperDetail->isIsCanceled()) {
                $subTotal += $purchaseOrderPaperDetail->getTotal();
            }
        }
        return $subTotal;
    }

    public function getSyncGrandTotal(): string
    {
        $grandTotal = round($this->getSubTotalAfterDiscount() + $this->taxNominal, 0);
        return $grandTotal;
    }

    public function getSyncTotalRemainingReceive(): int
    {
        $totalRemaining = 0;
        foreach ($this->purchaseOrderPaperDetails as $purchaseOrderPaperDetail) {
            if (!$purchaseOrderPaperDetail->isIsCanceled()) {
                $totalRemaining += $purchaseOrderPaperDetail->getRemainingReceive();
            }
        }
        return $totalRemaining;
    }

    public function getDiscountNominal(): string
    {
        return $this->discountValueType === self::DISCOUNT_VALUE_TYPE_NOMINAL ? $this->discountValue : $this->subTotal * $this->discountValue / 100;
    }

    public function getSubTotalAfterDiscount(): string
    {
        return $this->subTotal - $this->getDiscountNominal();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscountValueType(): ?string
    {
        return $this->discountValueType;
    }

    public function setDiscountValueType(string $discountValueType): self
    {
        $this->discountValueType = $discountValueType;

        return $this;
    }

    public function getDiscountValue(): ?string
    {
        return $this->discountValue;
    }

    public function setDiscountValue(string $discountValue): self
    {
        $this->discountValue = $discountValue;

        return $this;
    }

    public function getTaxMode(): ?string
    {
        return $this->taxMode;
    }

    public function setTaxMode(string $taxMode): self
    {
        $this->taxMode = $taxMode;

        return $this;
    }

    public function getTaxPercentage(): ?int
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(int $taxPercentage): self
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    public function getTaxNominal(): ?string
    {
        return $this->taxNominal;
    }

    public function setTaxNominal(string $taxNominal): self
    {
        $this->taxNominal = $taxNominal;

        return $this;
    }

    public function getSubTotal(): ?string
    {
        return $this->subTotal;
    }

    public function setSubTotal(string $subTotal): self
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getGrandTotal(): ?string
    {
        return $this->grandTotal;
    }

    public function setGrandTotal(string $grandTotal): self
    {
        $this->grandTotal = $grandTotal;

        return $this;
    }

    public function getTotalRemainingReceive(): ?int
    {
        return $this->totalRemainingReceive;
    }

    public function setTotalRemainingReceive(int $totalRemainingReceive): self
    {
        $this->totalRemainingReceive = $totalRemainingReceive;

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

    public function getApprovedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->approvedTransactionDateTime;
    }

    public function setApprovedTransactionDateTime(?\DateTimeInterface $approvedTransactionDateTime): self
    {
        $this->approvedTransactionDateTime = $approvedTransactionDateTime;

        return $this;
    }

    public function getRejectedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->rejectedTransactionDateTime;
    }

    public function setRejectedTransactionDateTime(?\DateTimeInterface $rejectedTransactionDateTime): self
    {
        $this->rejectedTransactionDateTime = $rejectedTransactionDateTime;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getApprovedTransactionUser(): ?User
    {
        return $this->approvedTransactionUser;
    }

    public function setApprovedTransactionUser(?User $approvedTransactionUser): self
    {
        $this->approvedTransactionUser = $approvedTransactionUser;

        return $this;
    }

    public function getRejectedTransactionUser(): ?User
    {
        return $this->rejectedTransactionUser;
    }

    public function setRejectedTransactionUser(?User $rejectedTransactionUser): self
    {
        $this->rejectedTransactionUser = $rejectedTransactionUser;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseOrderPaperDetail>
     */
    public function getPurchaseOrderPaperDetails(): Collection
    {
        return $this->purchaseOrderPaperDetails;
    }

    public function addPurchaseOrderPaperDetail(PurchaseOrderPaperDetail $purchaseOrderPaperDetail): self
    {
        if (!$this->purchaseOrderPaperDetails->contains($purchaseOrderPaperDetail)) {
            $this->purchaseOrderPaperDetails->add($purchaseOrderPaperDetail);
            $purchaseOrderPaperDetail->setPurchaseOrderPaperHeader($this);
        }

        return $this;
    }

    public function removePurchaseOrderPaperDetail(PurchaseOrderPaperDetail $purchaseOrderPaperDetail): self
    {
        if ($this->purchaseOrderPaperDetails->removeElement($purchaseOrderPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseOrderPaperDetail->getPurchaseOrderPaperHeader() === $this) {
                $purchaseOrderPaperDetail->setPurchaseOrderPaperHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReceiveHeader>
     */
    public function getReceiveHeaders(): Collection
    {
        return $this->receiveHeaders;
    }

    public function addReceiveHeader(ReceiveHeader $receiveHeader): self
    {
        if (!$this->receiveHeaders->contains($receiveHeader)) {
            $this->receiveHeaders->add($receiveHeader);
            $receiveHeader->setPurchaseOrderPaperHeader($this);
        }

        return $this;
    }

    public function removeReceiveHeader(ReceiveHeader $receiveHeader): self
    {
        if ($this->receiveHeaders->removeElement($receiveHeader)) {
            // set the owning side to null (unless already changed)
            if ($receiveHeader->getPurchaseOrderPaperHeader() === $this) {
                $receiveHeader->setPurchaseOrderPaperHeader(null);
            }
        }

        return $this;
    }

    public function isIsOnHold(): ?bool
    {
        return $this->isOnHold;
    }

    public function setIsOnHold(bool $isOnHold): self
    {
        $this->isOnHold = $isOnHold;

        return $this;
    }

    public function isHasReturnTransaction(): ?bool
    {
        return $this->hasReturnTransaction;
    }

    public function setHasReturnTransaction(bool $hasReturnTransaction): self
    {
        $this->hasReturnTransaction = $hasReturnTransaction;

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderHeader>
     */
    public function getMasterOrderHeaders(): Collection
    {
        return $this->masterOrderHeaders;
    }

    public function addMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if (!$this->masterOrderHeaders->contains($masterOrderHeader)) {
            $this->masterOrderHeaders->add($masterOrderHeader);
            $masterOrderHeader->setPurchaseOrderPaperHeader($this);
        }

        return $this;
    }

    public function removeMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if ($this->masterOrderHeaders->removeElement($masterOrderHeader)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderHeader->getPurchaseOrderPaperHeader() === $this) {
                $masterOrderHeader->setPurchaseOrderPaperHeader(null);
            }
        }

        return $this;
    }

    public function getRejectNote(): ?string
    {
        return $this->rejectNote;
    }

    public function setRejectNote(string $rejectNote): self
    {
        $this->rejectNote = $rejectNote;

        return $this;
    }

    public function getPurchaseOrderPaperList(): ?string
    {
        return $this->purchaseOrderPaperList;
    }

    public function setPurchaseOrderPaperList(string $purchaseOrderPaperList): self
    {
        $this->purchaseOrderPaperList = $purchaseOrderPaperList;

        return $this;
    }
}
