<?php

namespace App\Entity\Production;

use App\Entity\Master\DesignCodeProductDetail;
use App\Entity\Master\Product;
use App\Entity\ProductionDetail;
use App\Repository\Production\ProductPrototypeDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductPrototypeDetailRepository::class)]
#[ORM\Table(name: 'production_product_prototype_detail')]
class ProductPrototypeDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productPrototypeDetails')]
    #[Assert\NotNull]
    private ?ProductPrototype $productPrototype = null;

    #[ORM\ManyToOne(inversedBy: 'productPrototypeDetails')]
    private ?DesignCodeProductDetail $designCodeProductDetail = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\OneToMany(mappedBy: 'productPrototypeDetail', targetEntity: MasterOrderPrototypeDetail::class)]
    private Collection $masterOrderPrototypeDetails;

    #[ORM\OneToMany(mappedBy: 'productPrototypeDetail', targetEntity: ProductDevelopmentDetail::class)]
    private Collection $productDevelopmentDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    public function __construct()
    {
        $this->masterOrderPrototypeDetails = new ArrayCollection();
        $this->productDevelopmentDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDesignCodeProductDetail(): ?DesignCodeProductDetail
    {
        return $this->designCodeProductDetail;
    }

    public function setDesignCodeProductDetail(?DesignCodeProductDetail $designCodeProductDetail): self
    {
        $this->designCodeProductDetail = $designCodeProductDetail;

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

    /**
     * @return Collection<int, MasterOrderPrototypeDetail>
     */
    public function getMasterOrderPrototypeDetails(): Collection
    {
        return $this->masterOrderPrototypeDetails;
    }

    public function addMasterOrderPrototypeDetail(MasterOrderPrototypeDetail $masterOrderPrototypeDetail): self
    {
        if (!$this->masterOrderPrototypeDetails->contains($masterOrderPrototypeDetail)) {
            $this->masterOrderPrototypeDetails->add($masterOrderPrototypeDetail);
            $masterOrderPrototypeDetail->setProductPrototypeDetail($this);
        }

        return $this;
    }

    public function removeMasterOrderPrototypeDetail(MasterOrderPrototypeDetail $masterOrderPrototypeDetail): self
    {
        if ($this->masterOrderPrototypeDetails->removeElement($masterOrderPrototypeDetail)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderPrototypeDetail->getProductPrototypeDetail() === $this) {
                $masterOrderPrototypeDetail->setProductPrototypeDetail(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductDevelopmentDetail>
     */
    public function getProductDevelopmentDetails(): Collection
    {
        return $this->productDevelopmentDetails;
    }

    public function addProductDevelopmentDetail(ProductDevelopmentDetail $productDevelopmentDetail): self
    {
        if (!$this->productDevelopmentDetails->contains($productDevelopmentDetail)) {
            $this->productDevelopmentDetails->add($productDevelopmentDetail);
            $productDevelopmentDetail->setProductPrototypeDetail($this);
        }

        return $this;
    }

    public function removeProductDevelopmentDetail(ProductDevelopmentDetail $productDevelopmentDetail): self
    {
        if ($this->productDevelopmentDetails->removeElement($productDevelopmentDetail)) {
            // set the owning side to null (unless already changed)
            if ($productDevelopmentDetail->getProductPrototypeDetail() === $this) {
                $productDevelopmentDetail->setProductPrototypeDetail(null);
            }
        }

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
