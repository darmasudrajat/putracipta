<?php

namespace App\Entity\Admin;

use App\Repository\Admin\LiteralConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LiteralConfigRepository::class)]
#[ORM\Table(name: 'admin_literal_config')]
class LiteralConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $vatPercentage = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $serviceTaxPercentage = '0.00';

    #[ORM\Column(length: 60)]
    private ?string $ifscCode = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $paymentRemainingTolerance = '0.00';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVatPercentage(): ?int
    {
        return $this->vatPercentage;
    }

    public function setVatPercentage(int $vatPercentage): self
    {
        $this->vatPercentage = $vatPercentage;

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

    public function getIfscCode(): ?string
    {
        return $this->ifscCode;
    }

    public function setIfscCode(string $ifscCode): self
    {
        $this->ifscCode = $ifscCode;

        return $this;
    }

    public function getPaymentRemainingTolerance(): ?string
    {
        return $this->paymentRemainingTolerance;
    }

    public function setPaymentRemainingTolerance(string $paymentRemainingTolerance): self
    {
        $this->paymentRemainingTolerance = $paymentRemainingTolerance;

        return $this;
    }
}
