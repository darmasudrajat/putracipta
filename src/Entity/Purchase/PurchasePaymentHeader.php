<?php

namespace App\Entity\Purchase;

use App\Entity\Master\PaymentType;
use App\Entity\Master\Supplier;
use App\Entity\PurchaseHeader;
use App\Repository\Purchase\PurchasePaymentHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchasePaymentHeaderRepository::class)]
#[ORM\Table(name: 'purchase_purchase_payment_header')]
class PurchasePaymentHeader extends PurchaseHeader
{
    public const CODE_NUMBER_CONSTANT = 'PPY';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?PaymentType $paymentType = null;

    #[ORM\OneToMany(mappedBy: 'purchasePaymentHeader', targetEntity: PurchasePaymentDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $purchasePaymentDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $totalAmount = '0.00';

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $referenceNumber = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $currencyRate = '0.00';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $referenceDate = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $supplierInvoiceCodeNumbers = '';

    public function __construct()
    {
        $this->purchasePaymentDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalAmount(): string
    {
        $totalAmount = '0.00';
        foreach ($this->purchasePaymentDetails as $purchasePaymentDetail) {
            if (!$purchasePaymentDetail->isIsCanceled()) {
                $totalAmount += $purchasePaymentDetail->getAmount();
            }
        }
        return $totalAmount;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaymentType(): ?PaymentType
    {
        return $this->paymentType;
    }

    public function setPaymentType(?PaymentType $paymentType): self
    {
        $this->paymentType = $paymentType;

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
            $purchasePaymentDetail->setPurchasePaymentHeader($this);
        }

        return $this;
    }

    public function removePurchasePaymentDetail(PurchasePaymentDetail $purchasePaymentDetail): self
    {
        if ($this->purchasePaymentDetails->removeElement($purchasePaymentDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchasePaymentDetail->getPurchasePaymentHeader() === $this) {
                $purchasePaymentDetail->setPurchasePaymentHeader(null);
            }
        }

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
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

    public function getCurrencyRate(): ?string
    {
        return $this->currencyRate;
    }

    public function setCurrencyRate(string $currencyRate): self
    {
        $this->currencyRate = $currencyRate;

        return $this;
    }

    public function getReferenceDate(): ?\DateTimeInterface
    {
        return $this->referenceDate;
    }

    public function setReferenceDate(?\DateTimeInterface $referenceDate): self
    {
        $this->referenceDate = $referenceDate;

        return $this;
    }

    public function getSupplierInvoiceCodeNumbers(): ?string
    {
        return $this->supplierInvoiceCodeNumbers;
    }

    public function setSupplierInvoiceCodeNumbers(string $supplierInvoiceCodeNumbers): self
    {
        $this->supplierInvoiceCodeNumbers = $supplierInvoiceCodeNumbers;

        return $this;
    }
}
