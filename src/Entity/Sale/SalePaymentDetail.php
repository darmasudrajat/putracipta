<?php

namespace App\Entity\Sale;

use App\Entity\Master\Account;
use App\Entity\SaleDetail;
use App\Repository\Sale\SalePaymentDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SalePaymentDetailRepository::class)]
#[ORM\Table(name: 'sale_sale_payment_detail')]
class SalePaymentDetail extends SaleDetail
{
    public const SERVICE_TAX_MODE_NON_TAX = 'non_service_tax';
    public const SERVICE_TAX_MODE_TAX = 'service_tax';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $amount = '0.00';

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'salePaymentDetails')]
    #[Assert\NotNull]
    private ?SaleInvoiceHeader $saleInvoiceHeader = null;

    #[ORM\ManyToOne(inversedBy: 'salePaymentDetails')]
    #[Assert\NotNull]
    private ?SalePaymentHeader $salePaymentHeader = null;

    #[ORM\Column(length: 20)]
    private ?string $serviceTaxMode = self::SERVICE_TAX_MODE_NON_TAX;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $serviceTaxPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $serviceTaxNominal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $receivableAmount = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $invoiceAmount = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->salePaymentHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncInvoiceAmount(): string 
    {
        $saleInvoiceHeader = $this->getSaleInvoiceHeader();
        
        return $saleInvoiceHeader->getGrandTotal() - $saleInvoiceHeader->getTotalReturn();
    }

    public function getSyncServiceTaxNominal(): string
    {
        $saleInvoiceHeader = $this->getSaleInvoiceHeader();
        
        return $saleInvoiceHeader->getSubTotal() * $this->serviceTaxPercentage / 100;
    }
    
    public function getSyncReceivableAmount(): string 
    {
        return $this->invoiceAmount - $this->serviceTaxNominal;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

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

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

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

    public function getSalePaymentHeader(): ?SalePaymentHeader
    {
        return $this->salePaymentHeader;
    }

    public function setSalePaymentHeader(?SalePaymentHeader $salePaymentHeader): self
    {
        $this->salePaymentHeader = $salePaymentHeader;

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

    public function getReceivableAmount(): ?string
    {
        return $this->receivableAmount;
    }

    public function setReceivableAmount(string $receivableAmount): self
    {
        $this->receivableAmount = $receivableAmount;

        return $this;
    }

    public function getInvoiceAmount(): ?string
    {
        return $this->invoiceAmount;
    }

    public function setInvoiceAmount(string $invoiceAmount): self
    {
        $this->invoiceAmount = $invoiceAmount;

        return $this;
    }
}
