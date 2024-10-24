<?php

namespace App\Entity\Sale;

use App\Entity\Admin\User;
use App\Entity\Master\Customer;
use App\Entity\Master\Employee;
use App\Entity\SaleHeader;
use App\Repository\Sale\SaleOrderHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleOrderHeaderRepository::class)]
#[ORM\Table(name: 'sale_sale_order_header')]
#[UniqueEntity('referenceNumber')]
class SaleOrderHeader extends SaleHeader
{
    public const CODE_NUMBER_CONSTANT = 'SO';
    public const DISCOUNT_VALUE_TYPE_PERCENTAGE = 'percentage';
    public const DISCOUNT_VALUE_TYPE_NOMINAL = 'nominal';
    public const TAX_MODE_NON_TAX = 'non_tax';
    public const TAX_MODE_TAX_EXCLUSION = 'tax_exclusion';
    public const TAX_MODE_TAX_INCLUSION = 'tax_inclusion';
    public const TRANSACTION_STATUS_DRAFT = 'draft';
    public const TRANSACTION_STATUS_DONE = 'done';
    public const TRANSACTION_STATUS_APPROVE = 'approve';
    public const TRANSACTION_STATUS_REJECT = 'reject';
    public const TRANSACTION_STATUS_HOLD = 'hold';
    public const TRANSACTION_STATUS_RELEASE = 'release';
    public const TRANSACTION_STATUS_PARTIAL_DELIVERY = 'partial_delivery';
    public const TRANSACTION_STATUS_FULL_DELIVERY = 'full_delivery';
    public const TRANSACTION_STATUS_CANCEL = 'cancelled';
    public const TRANSACTION_TYPE_PRODUCTION = 'production';
    public const TRANSACTION_TYPE_INTERNAL = 'internal';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60, unique: true)]
    #[Assert\NotBlank]
    private ?string $referenceNumber = '';

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_DRAFT;

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

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'saleOrderHeader', targetEntity: SaleOrderDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $saleOrderDetails;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $transactionFileExtension = '';

    #[ORM\ManyToOne]
    private ?Employee $employee = null;

    #[ORM\Column]
    private ?bool $isUsingFscPaper = false;

    #[ORM\Column]
    private ?bool $isOnHold = false;

    #[ORM\Column(length: 100)]
    private ?string $customerName = '';

    #[ORM\Column(type: Types::SMALLINT)]
//    #[Assert\GreaterThan(0)]
    #[Assert\LessThan(16)]
    private ?int $deliveryAddressOrdinal = 0;

    #[ORM\Column]
    private ?bool $hasReturnTransaction = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvedTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $rejectedTransactionDateTime = null;

    #[ORM\ManyToOne]
    private ?User $approvedTransactionUser = null;

    #[ORM\ManyToOne]
    private ?User $rejectedTransactionUser = null;

    #[ORM\Column]
    #[Assert\NotNull]
    protected ?bool $isRead = false;

    #[ORM\Column(length: 60)]
    private ?string $transactionType = self::TRANSACTION_TYPE_PRODUCTION;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $orderReceiveDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalRemainingDelivery = '0.00';

    public function __construct()
    {
        $this->saleOrderDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): string
    {
        $totalQuantity = 0;
        foreach ($this->saleOrderDetails as $saleOrderDetail) {
            if (!$saleOrderDetail->isIsCanceled()) {
                $totalQuantity += $saleOrderDetail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getSyncTaxNominal(): string
    {
        return $this->getSubTotalAfterDiscount() * $this->taxPercentage / 100;
    }

    public function getSyncSubTotal(): string
    {
        $subTotal = '0.00';
        foreach ($this->saleOrderDetails as $saleOrderDetail) {
            if (!$saleOrderDetail->isIsCanceled()) {
                $subTotal += $saleOrderDetail->getTotal();
            }
        }
        return $subTotal;
    }

    public function getSyncGrandTotal(): string
    {
        return round($this->getSubTotalAfterDiscount() + $this->taxNominal, 0);
    }

    public function getSyncTotalRemainingDelivery(): int
    {
        $total = 0;
        foreach ($this->saleOrderDetails as $saleOrderDetail) {
            if (!$saleOrderDetail->isIsCanceled()) {
                $total += $saleOrderDetail->getRemainingQuantityDelivery();
            }
        }
        return $total;
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

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;

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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, SaleOrderDetail>
     */
    public function getSaleOrderDetails(): Collection
    {
        return $this->saleOrderDetails;
    }

    public function addSaleOrderDetail(SaleOrderDetail $saleOrderDetail): self
    {
        if (!$this->saleOrderDetails->contains($saleOrderDetail)) {
            $this->saleOrderDetails->add($saleOrderDetail);
            $saleOrderDetail->setSaleOrderHeader($this);
        }

        return $this;
    }

    public function removeSaleOrderDetail(SaleOrderDetail $saleOrderDetail): self
    {
        if ($this->saleOrderDetails->removeElement($saleOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleOrderDetail->getSaleOrderHeader() === $this) {
                $saleOrderDetail->setSaleOrderHeader(null);
            }
        }

        return $this;
    }

    public function getTransactionFileExtension(): ?string
    {
        return $this->transactionFileExtension;
    }

    public function setTransactionFileExtension(string $transactionFileExtension): self
    {
        $this->transactionFileExtension = $transactionFileExtension;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function isIsUsingFscPaper(): ?bool
    {
        return $this->isUsingFscPaper;
    }

    public function setIsUsingFscPaper(bool $isUsingFscPaper): self
    {
        $this->isUsingFscPaper = $isUsingFscPaper;

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

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getDeliveryAddressOrdinal(): ?int
    {
        return $this->deliveryAddressOrdinal;
    }

    public function setDeliveryAddressOrdinal(int $deliveryAddressOrdinal): self
    {
        $this->deliveryAddressOrdinal = $deliveryAddressOrdinal;

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

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getOrderReceiveDate(): ?\DateTimeInterface
    {
        return $this->orderReceiveDate;
    }

    public function setOrderReceiveDate(?\DateTimeInterface $orderReceiveDate): self
    {
        $this->orderReceiveDate = $orderReceiveDate;

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

    public function getTotalRemainingDelivery(): ?string
    {
        return $this->totalRemainingDelivery;
    }

    public function setTotalRemainingDelivery(string $totalRemainingDelivery): self
    {
        $this->totalRemainingDelivery = $totalRemainingDelivery;

        return $this;
    }
}
