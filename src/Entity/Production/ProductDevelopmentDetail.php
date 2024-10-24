<?php

namespace App\Entity\Production;

use App\Entity\Master\Product;
use App\Entity\ProductionDetail;
use App\Repository\Production\ProductDevelopmentDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductDevelopmentDetailRepository::class)]
#[ORM\Table(name: 'production_product_development_detail')]
class ProductDevelopmentDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productDevelopmentDetails')]
    #[Assert\NotNull]
    private ?ProductDevelopment $product_development = null;

    #[ORM\ManyToOne(inversedBy: 'productDevelopmentDetails')]
    private ?ProductPrototypeDetail $productPrototypeDetail = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductDevelopment(): ?ProductDevelopment
    {
        return $this->product_development;
    }

    public function setProductDevelopment(?ProductDevelopment $product_development): self
    {
        $this->product_development = $product_development;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
