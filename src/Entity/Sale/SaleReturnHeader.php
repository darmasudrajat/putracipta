<?php

namespace App\Entity\Sale;

use App\Entity\Master\Customer;
use App\Entity\Master\Warehouse;
use App\Entity\SaleHeader;
use App\Repository\Sale\SaleReturnHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleReturnHeaderRepository::class)]
#[ORM\Table(name: 'sale_sale_return_header')]
class SaleReturnHeader extends SaleHeader
{
    public const CODE_NUMBER_CONSTANT = 'SRT';
    public const TAX_MODE_NON_TAX = 'non_tax';
    public const TAX_MODE_TAX_EXCLUSION = 'tax_exclusion';
    public const TAX_MODE_TAX_INCLUSION = 'tax_inclusion';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'saleReturnHeader', targetEntity: SaleReturnDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $saleReturnDetails;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $saleOrderReferenceNumbers = '';

    #[ORM\Column]
    private ?bool $isProductExchange = false;

    #[ORM\ManyToOne(inversedBy: 'saleReturnHeaders')]
    #[Assert\NotNull]
    private ?DeliveryHeader $deliveryHeader = null;

    #[ORM\Column(length: 100)]
    private ?string $referenceNumber = '';

    #[ORM\Column(length: 100)]
    private ?string $taxNumber = '';

    public function __construct()
    {
        $this->saleReturnDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTaxNominal(): string
    {
        $taxNominal = $this->subTotal * $this->taxPercentage / 100;
        return $taxNominal;
    }

    public function getSyncSubTotal(): string
    {
        $subTotal = '0.00';
        foreach ($this->saleReturnDetails as $saleReturnDetail) {
            if (!$saleReturnDetail->isIsCanceled()) {
                $subTotal += $saleReturnDetail->getTotal();
            }
        }
        return $subTotal;
    }

    public function getSyncGrandTotal(): string
    {
        $grandTotal = $this->subTotal + $this->taxNominal;
        return $grandTotal;
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, SaleReturnDetail>
     */
    public function getSaleReturnDetails(): Collection
    {
        return $this->saleReturnDetails;
    }

    public function addSaleReturnDetail(SaleReturnDetail $saleReturnDetail): self
    {
        if (!$this->saleReturnDetails->contains($saleReturnDetail)) {
            $this->saleReturnDetails->add($saleReturnDetail);
            $saleReturnDetail->setSaleReturnHeader($this);
        }

        return $this;
    }

    public function removeSaleReturnDetail(SaleReturnDetail $saleReturnDetail): self
    {
        if ($this->saleReturnDetails->removeElement($saleReturnDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleReturnDetail->getSaleReturnHeader() === $this) {
                $saleReturnDetail->setSaleReturnHeader(null);
            }
        }

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

    public function getSaleOrderReferenceNumbers(): ?string
    {
        return $this->saleOrderReferenceNumbers;
    }

    public function setSaleOrderReferenceNumbers(string $saleOrderReferenceNumbers): self
    {
        $this->saleOrderReferenceNumbers = $saleOrderReferenceNumbers;

        return $this;
    }

    public function isIsProductExchange(): ?bool
    {
        return $this->isProductExchange;
    }

    public function setIsProductExchange(bool $isProductExchange): self
    {
        $this->isProductExchange = $isProductExchange;

        return $this;
    }

    public function getDeliveryHeader(): ?DeliveryHeader
    {
        return $this->deliveryHeader;
    }

    public function setDeliveryHeader(?DeliveryHeader $deliveryHeader): self
    {
        $this->deliveryHeader = $deliveryHeader;

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

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(string $taxNumber): self
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }
}
