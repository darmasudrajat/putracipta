<?php

namespace App\Entity\Sale;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\SaleDetail;
use App\Repository\Sale\SaleInvoiceDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleInvoiceDetailRepository::class)]
#[ORM\Table(name: 'sale_sale_invoice_detail')]
class SaleInvoiceDetail extends SaleDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 6)]
    #[Assert\Type('numeric')]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'saleInvoiceDetails')]
    #[Assert\NotNull]
    private ?SaleInvoiceHeader $saleInvoiceHeader = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $return_amount = '0.00';

    #[ORM\ManyToOne(inversedBy: 'saleInvoiceDetails')]
    private ?DeliveryDetail $deliveryDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $unitPriceBeforeTax = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->saleInvoiceHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncUnitPriceBeforeTax(): string
    {
        return $this->saleInvoiceHeader->getTaxMode() === $this->saleInvoiceHeader::TAX_MODE_TAX_INCLUSION ? round($this->unitPrice / (1 + $this->saleInvoiceHeader->getTaxPercentage() / 100), 2) : $this->unitPrice;
    }

    public function getTotal(): string
    {
        if ($this->saleInvoiceHeader === null) {
            return '0.00';
        }
        return $this->saleInvoiceHeader->getTaxMode() === $this->saleInvoiceHeader::TAX_MODE_TAX_INCLUSION ? round($this->quantity * $this->unitPrice / (1 + $this->saleInvoiceHeader->getTaxPercentage() / 100), 2) : $this->quantity * $this->unitPrice;
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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSaleInvoiceHeader(): ?SaleInvoiceHeader
    {
        return $this->saleInvoiceHeader;
    }

    public function setSaleInvoiceHeader(?SaleInvoiceHeader $saleInvoiceHeader): self
    {
        $this->saleInvoiceHeader = $saleInvoiceHeader;

        return $this;
    }

    public function getReturnAmount(): ?string
    {
        return $this->return_amount;
    }

    public function setReturnAmount(string $return_amount): self
    {
        $this->return_amount = $return_amount;

        return $this;
    }

    public function getDeliveryDetail(): ?DeliveryDetail
    {
        return $this->deliveryDetail;
    }

    public function setDeliveryDetail(?DeliveryDetail $deliveryDetail): self
    {
        $this->deliveryDetail = $deliveryDetail;

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

    public function getUnitPriceBeforeTax(): ?string
    {
        return $this->unitPriceBeforeTax;
    }

    public function setUnitPriceBeforeTax(string $unitPriceBeforeTax): self
    {
        $this->unitPriceBeforeTax = $unitPriceBeforeTax;

        return $this;
    }
}
