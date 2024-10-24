<?php

namespace App\Entity\Production;

use App\Entity\ProductionDetail;
use App\Repository\Production\ProductPrototypePilotDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductPrototypePilotDetailRepository::class)]
#[ORM\Table(name: 'production_product_prototype_pilot_detail')]
class ProductPrototypePilotDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $productName = '';

    #[ORM\Column(length: 60)]
    private ?string $size = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    #[ORM\ManyToOne(inversedBy: 'productPrototypePilotDetails')]
    #[Assert\NotNull]
    private ?ProductPrototype $productPrototype = null;

    #[ORM\Column(length: 200)]
    private ?string $memo = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

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

    public function getProductPrototype(): ?ProductPrototype
    {
        return $this->productPrototype;
    }

    public function setProductPrototype(?ProductPrototype $productPrototype): self
    {
        $this->productPrototype = $productPrototype;

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
}
