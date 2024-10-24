<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Material;
use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\PurchaseDetail;
use App\Repository\Purchase\PurchaseReturnDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseReturnDetailRepository::class)]
#[ORM\Table(name: 'purchase_purchase_return_detail')]
class PurchaseReturnDetail extends PurchaseDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseReturnDetails')]
    private ?ReceiveDetail $receiveDetail = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseReturnDetails')]
    #[Assert\NotNull]
    private ?PurchaseReturnHeader $purchaseReturnHeader = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseReturnHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getTotal(): string
    {
        return $this->quantity * $this->unitPrice;
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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getReceiveDetail(): ?ReceiveDetail
    {
        return $this->receiveDetail;
    }

    public function setReceiveDetail(?ReceiveDetail $receiveDetail): self
    {
        $this->receiveDetail = $receiveDetail;

        return $this;
    }

    public function getPurchaseReturnHeader(): ?PurchaseReturnHeader
    {
        return $this->purchaseReturnHeader;
    }

    public function setPurchaseReturnHeader(?PurchaseReturnHeader $purchaseReturnHeader): self
    {
        $this->purchaseReturnHeader = $purchaseReturnHeader;

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

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

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
}
