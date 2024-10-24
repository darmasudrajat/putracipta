<?php

namespace App\Entity\Production;

use App\Entity\Master\Product;
use App\Entity\ProductionDetail;
use App\Repository\Production\MasterOrderPrototypeDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MasterOrderPrototypeDetailRepository::class)]
#[ORM\Table(name: 'production_master_order_prototype_detail')]
class MasterOrderPrototypeDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderPrototypeDetails')]
    #[Assert\NotNull]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderPrototypeDetails')]
    private ?ProductPrototypeDetail $productPrototypeDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityOrder = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityStock = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityShortage = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->masterOrderHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityShortage() 
    {
        return $this->quantityOrder - $this->quantityStock;
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getMasterOrderHeader(): ?MasterOrderHeader
    {
        return $this->masterOrderHeader;
    }

    public function setMasterOrderHeader(?MasterOrderHeader $masterOrderHeader): self
    {
        $this->masterOrderHeader = $masterOrderHeader;

        return $this;
    }

    public function getProductPrototypeDetail(): ?ProductPrototypeDetail
    {
        return $this->productPrototypeDetail;
    }

    public function setProductPrototypeDetail(?ProductPrototypeDetail $productPrototypeDetail): self
    {
        $this->productPrototypeDetail = $productPrototypeDetail;

        return $this;
    }

    public function getQuantityOrder(): ?string
    {
        return $this->quantityOrder;
    }

    public function setQuantityOrder(string $quantityOrder): self
    {
        $this->quantityOrder = $quantityOrder;

        return $this;
    }

    public function getQuantityStock(): ?string
    {
        return $this->quantityStock;
    }

    public function setQuantityStock(string $quantityStock): self
    {
        $this->quantityStock = $quantityStock;

        return $this;
    }

    public function getQuantityShortage(): ?string
    {
        return $this->quantityShortage;
    }

    public function setQuantityShortage(string $quantityShortage): self
    {
        $this->quantityShortage = $quantityShortage;

        return $this;
    }
}
