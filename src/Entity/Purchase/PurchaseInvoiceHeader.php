<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Supplier;
use App\Entity\PurchaseHeader;
use App\Repository\Purchase\PurchaseInvoiceHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseInvoiceHeaderRepository::class)]
#[ORM\Table(name: 'purchase_purchase_invoice_header')]
class PurchaseInvoiceHeader extends PurchaseHeader
{
    public const CODE_NUMBER_CONSTANT = 'PIN';
    public const DISCOUNT_VALUE_TYPE_PERCENTAGE = 'percentage';
    public const DISCOUNT_VALUE_TYPE_NOMINAL = 'nominal';
    public const TAX_MODE_NON_TAX = 'non_tax';
    public const TAX_MODE_TAX_EXCLUSION = 'tax_exclusion';
    public const TAX_MODE_TAX_INCLUSION = 'tax_inclusion';
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

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $supplierInvoiceCodeNumber = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $discountValueType = self::DISCOUNT_VALUE_TYPE_PERCENTAGE;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    #[Assert\GreaterThanOrEqual(0)]
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

    #[ORM\ManyToOne]
    private ?Supplier $supplier = null;

    #[ORM\OneToMany(mappedBy: 'purchaseInvoiceHeader', targetEntity: PurchaseInvoiceDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $purchaseInvoiceDetails;

    #[ORM\OneToMany(mappedBy: 'purchaseInvoiceHeader', targetEntity: PurchasePaymentDetail::class)]
    private Collection $purchasePaymentDetails;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $invoiceTaxDate = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_INVOICING;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $invoiceReceivedDate = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseInvoiceHeaders')]
    #[Assert\NotNull]
    private ?ReceiveHeader $receiveHeader = null;

    public function __construct()
    {
        $this->purchaseInvoiceDetails = new ArrayCollection();
        $this->purchasePaymentDetails = new ArrayCollection();
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
        foreach ($this->purchaseInvoiceDetails as $purchaseInvoiceDetail) {
            if (!$purchaseInvoiceDetail->isIsCanceled()) {
                $subTotal += $purchaseInvoiceDetail->getTotal();
            }
        }
        return $subTotal;
    }

    public function getSyncGrandTotal(): string
    {
        $grandTotal = $this->getSubTotalAfterDiscount() + $this->taxNominal;
        return $grandTotal;
    }

    public function getSyncDueDate(): ?\DateTimeInterface
    {
        $paymentTerm = $this->supplier === null ? 0 : $this->supplier->getPaymentTerm();
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

    public function getSupplierInvoiceCodeNumber(): ?string
    {
        return $this->supplierInvoiceCodeNumber;
    }

    public function setSupplierInvoiceCodeNumber(string $supplierInvoiceCodeNumber): self
    {
        $this->supplierInvoiceCodeNumber = $supplierInvoiceCodeNumber;

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

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseInvoiceDetail>
     */
    public function getPurchaseInvoiceDetails(): Collection
    {
        return $this->purchaseInvoiceDetails;
    }

    public function addPurchaseInvoiceDetail(PurchaseInvoiceDetail $purchaseInvoiceDetail): self
    {
        if (!$this->purchaseInvoiceDetails->contains($purchaseInvoiceDetail)) {
            $this->purchaseInvoiceDetails->add($purchaseInvoiceDetail);
            $purchaseInvoiceDetail->setPurchaseInvoiceHeader($this);
        }

        return $this;
    }

    public function removePurchaseInvoiceDetail(PurchaseInvoiceDetail $purchaseInvoiceDetail): self
    {
        if ($this->purchaseInvoiceDetails->removeElement($purchaseInvoiceDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseInvoiceDetail->getPurchaseInvoiceHeader() === $this) {
                $purchaseInvoiceDetail->setPurchaseInvoiceHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PurchasePaymentDetail>
     */
    public function getPurchasePaymentDetails(): Collection
    {
        return $this->purchasePaymentDetails;
    }

    public function addPurchasePaymentDetail(PurchasePaymentDetail $purchasePaymentDetail): self
    {
        if (!$this->purchasePaymentDetails->contains($purchasePaymentDetail)) {
            $this->purchasePaymentDetails->add($purchasePaymentDetail);
            $purchasePaymentDetail->setPurchaseInvoiceHeader($this);
        }

        return $this;
    }

    public function removePurchasePaymentDetail(PurchasePaymentDetail $purchasePaymentDetail): self
    {
        if ($this->purchasePaymentDetails->removeElement($purchasePaymentDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchasePaymentDetail->getPurchaseInvoiceHeader() === $this) {
                $purchasePaymentDetail->setPurchaseInvoiceHeader(null);
            }
        }

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

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

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

    public function getInvoiceReceivedDate(): ?\DateTimeInterface
    {
        return $this->invoiceReceivedDate;
    }

    public function setInvoiceReceivedDate(?\DateTimeInterface $invoiceReceivedDate): self
    {
        $this->invoiceReceivedDate = $invoiceReceivedDate;

        return $this;
    }

    public function getReceiveHeader(): ?ReceiveHeader
    {
        return $this->receiveHeader;
    }

    public function setReceiveHeader(?ReceiveHeader $receiveHeader): self
    {
        $this->receiveHeader = $receiveHeader;

        return $this;
    }
}
