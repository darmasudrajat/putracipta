<?php

namespace App\Entity\Sale;

use App\Entity\Master\Customer;
use App\Entity\SaleHeader;
use App\Repository\Sale\SaleInvoiceHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleInvoiceHeaderRepository::class)]
#[ORM\Table(name: 'sale_sale_invoice_header')]
class SaleInvoiceHeader extends SaleHeader
{
    public const CODE_NUMBER_CONSTANT = 'INV';
    public const DISCOUNT_VALUE_TYPE_PERCENTAGE = 'percentage';
    public const DISCOUNT_VALUE_TYPE_NOMINAL = 'nominal';
    public const TAX_MODE_NON_TAX = 'non_tax';
    public const TAX_MODE_TAX_EXCLUSION = 'tax_exclusion';
    public const TAX_MODE_TAX_INCLUSION = 'tax_inclusion';
    public const SERVICE_TAX_MODE_NON_TAX = 'non_service_tax';
    public const SERVICE_TAX_MODE_TAX = 'service_tax';
    public const TRANSACTION_STATUS_INVOICING = 'invoicing';
    public const TRANSACTION_STATUS_PARTIAL_PAYMENT = 'partial_payment';
    public const TRANSACTION_STATUS_FULL_PAYMENT = 'full_payment';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $invoiceTaxCodeNumber = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $discountValueType = self::DISCOUNT_VALUE_TYPE_PERCENTAGE;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
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

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $totalPayment = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $totalReturn = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $remainingPayment = '0.00';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $invoiceTaxDate = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_INVOICING;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'saleInvoiceHeader', targetEntity: SalePaymentDetail::class)]
    private Collection $salePaymentDetails;

    #[ORM\OneToMany(mappedBy: 'saleInvoiceHeader', targetEntity: SaleInvoiceDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $saleInvoiceDetails;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $serviceTaxMode = self::SERVICE_TAX_MODE_NON_TAX;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    private ?string $serviceTaxPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotBlank]
    private ?string $serviceTaxNominal = '0.00';

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $isUsingFscPaper = false;

    #[ORM\Column(length: 100)]
    private ?string $saleOrderReferenceNumbers = '';

    #[ORM\Column]
    #[Assert\NotNull]
    protected ?bool $isRead = false;

    #[ORM\Column(length: 100)]
    private ?string $deliveryReferenceNumbers = '';

    #[ORM\Column(length: 20)]
    private ?string $customerTaxNumber = '';

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\GreaterThan(0)]
    #[Assert\LessThan(6)]
    private ?int $customerAddressTaxOrdinal = 0;

    public function __construct()
    {
        $this->salePaymentDetails = new ArrayCollection();
        $this->saleInvoiceDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTaxNominal(): string
    {
        return $this->getSubTotalAfterDiscount() * $this->taxPercentage / 100;
    }

    public function getSyncTotalReturn(): string
    {
        $totalReturn = '0.00';
        foreach ($this->saleInvoiceDetails as $saleInvoiceDetail) {
            if (!$saleInvoiceDetail->isIsCanceled()) {
                $totalReturn += $saleInvoiceDetail->getReturnAmount();
            }
        }
        return $totalReturn;
    }

    public function getSyncSubTotal(): string
    {
        $subTotal = '0.00';
        foreach ($this->saleInvoiceDetails as $saleInvoiceDetail) {
            if (!$saleInvoiceDetail->isIsCanceled()) {
                $subTotal += $saleInvoiceDetail->getTotal();
            }
        }
        return $subTotal;
    }

    public function getSyncGrandTotal(): string
    {
        return $this->getSubTotalAfterDiscount() + $this->taxNominal; // - $this->serviceTaxNominal;
    }

    public function getSyncDueDate(): ?\DateTimeInterface
    {
        $paymentTerm = $this->customer === null ? 0 : $this->customer->getPaymentTerm();
        $dueDate = null;
        if ($this->transactionDate !== null) {
            $dueDate = \DateTime::createFromInterface($this->transactionDate);
            $dueDate->add(new \DateInterval("P{$paymentTerm}D"));
        }
        return $dueDate;
    }

    public function getSyncRemainingPayment(): string
    {
        return $this->grandTotal - $this->totalPayment - $this->totalReturn;
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

    public function getInvoiceTaxCodeNumber(): ?string
    {
        return $this->invoiceTaxCodeNumber;
    }

    public function setInvoiceTaxCodeNumber(string $invoiceTaxCodeNumber): self
    {
        $this->invoiceTaxCodeNumber = $invoiceTaxCodeNumber;

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

    public function getTotalPayment(): ?string
    {
        return $this->totalPayment;
    }

    public function setTotalPayment(string $totalPayment): self
    {
        $this->totalPayment = $totalPayment;

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

    public function getRemainingPayment(): ?string
    {
        return $this->remainingPayment;
    }

    public function setRemainingPayment(string $remainingPayment): self
    {
        $this->remainingPayment = $remainingPayment;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getInvoiceTaxDate(): ?\DateTimeInterface
    {
        return $this->invoiceTaxDate;
    }

    public function setInvoiceTaxDate(?\DateTimeInterface $invoiceTaxDate): self
    {
        $this->invoiceTaxDate = $invoiceTaxDate;

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
     * @return Collection<int, SalePaymentDetail>
     */
    public function getSalePaymentDetails(): Collection
    {
        return $this->salePaymentDetails;
    }

    public function addSalePaymentDetail(SalePaymentDetail $salePaymentDetail): self
    {
        if (!$this->salePaymentDetails->contains($salePaymentDetail)) {
            $this->salePaymentDetails->add($salePaymentDetail);
            $salePaymentDetail->setSaleInvoiceHeader($this);
        }

        return $this;
    }

    public function removeSalePaymentDetail(SalePaymentDetail $salePaymentDetail): self
    {
        if ($this->salePaymentDetails->removeElement($salePaymentDetail)) {
            // set the owning side to null (unless already changed)
            if ($salePaymentDetail->getSaleInvoiceHeader() === $this) {
                $salePaymentDetail->setSaleInvoiceHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SaleInvoiceDetail>
     */
    public function getSaleInvoiceDetails(): Collection
    {
        return $this->saleInvoiceDetails;
    }

    public function addSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail): self
    {
        if (!$this->saleInvoiceDetails->contains($saleInvoiceDetail)) {
            $this->saleInvoiceDetails->add($saleInvoiceDetail);
            $saleInvoiceDetail->setSaleInvoiceHeader($this);
        }

        return $this;
    }

    public function removeSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail): self
    {
        if ($this->saleInvoiceDetails->removeElement($saleInvoiceDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleInvoiceDetail->getSaleInvoiceHeader() === $this) {
                $saleInvoiceDetail->setSaleInvoiceHeader(null);
            }
        }

        return $this;
    }

    public function getServiceTaxMode(): ?string
    {
        return $this->serviceTaxMode;
    }

    public function setServiceTaxMode(string $serviceTaxMode): self
    {
        $this->serviceTaxMode = $serviceTaxMode;

        return $this;
    }

    public function getServiceTaxPercentage(): ?string
    {
        return $this->serviceTaxPercentage;
    }

    public function setServiceTaxPercentage(string $serviceTaxPercentage): self
    {
        $this->serviceTaxPercentage = $serviceTaxPercentage;

        return $this;
    }

    public function getServiceTaxNominal(): ?string
    {
        return $this->serviceTaxNominal;
    }

    public function setServiceTaxNominal(string $serviceTaxNominal): self
    {
        $this->serviceTaxNominal = $serviceTaxNominal;

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

    public function getSaleOrderReferenceNumbers(): ?string
    {
        return $this->saleOrderReferenceNumbers;
    }

    public function setSaleOrderReferenceNumbers(string $saleOrderReferenceNumbers): self
    {
        $this->saleOrderReferenceNumbers = $saleOrderReferenceNumbers;

        return $this;
    }

    public function getDeliveryReferenceNumbers(): ?string
    {
        return $this->deliveryReferenceNumbers;
    }

    public function setDeliveryReferenceNumbers(string $deliveryReferenceNumbers): self
    {
        $this->deliveryReferenceNumbers = $deliveryReferenceNumbers;

        return $this;
    }

    public function getCustomerTaxNumber(): ?string
    {
        return $this->customerTaxNumber;
    }

    public function setCustomerTaxNumber(string $customerTaxNumber): self
    {
        $this->customerTaxNumber = $customerTaxNumber;

        return $this;
    }

    public function getCustomerAddressTaxOrdinal(): ?int
    {
        return $this->customerAddressTaxOrdinal;
    }

    public function setCustomerAddressTaxOrdinal(int $customerAddressTaxOrdinal): self
    {
        $this->customerAddressTaxOrdinal = $customerAddressTaxOrdinal;

        return $this;
    }
}
