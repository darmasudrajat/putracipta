<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Entity\Production\ProductPrototypeDetail;
use App\Repository\Master\DesignCodeProductDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DesignCodeProductDetailRepository::class)]
#[ORM\Table(name: 'master_design_code_product_detail')]
class DesignCodeProductDetail extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'designCodeProductDetails')]
    #[Assert\NotNull]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'designCodeProductDetails')]
    #[Assert\NotNull]
    private ?DesignCode $designCode = null;

    #[ORM\OneToMany(mappedBy: 'designCodeProductDetail', targetEntity: ProductPrototypeDetail::class)]
    private Collection $productPrototypeDetails;

    public function __construct()
    {
        $this->productPrototypeDetails = new ArrayCollection();
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

    public function getDesignCode(): ?DesignCode
    {
        return $this->designCode;
    }

    public function setDesignCode(?DesignCode $designCode): self
    {
        $this->designCode = $designCode;

        return $this;
    }

    /**
     * @return Collection<int, ProductPrototypeDetail>
     */
    public function getProductPrototypeDetails(): Collection
    {
        return $this->productPrototypeDetails;
    }

    public function addProductPrototypeDetail(ProductPrototypeDetail $productPrototypeDetail): self
    {
        if (!$this->productPrototypeDetails->contains($productPrototypeDetail)) {
            $this->productPrototypeDetails->add($productPrototypeDetail);
            $productPrototypeDetail->setDesignCodeProductDetail($this);
        }

        return $this;
    }

    public function removeProductPrototypeDetail(ProductPrototypeDetail $productPrototypeDetail): self
    {
        if ($this->productPrototypeDetails->removeElement($productPrototypeDetail)) {
            // set the owning side to null (unless already changed)
            if ($productPrototypeDetail->getDesignCodeProductDetail() === $this) {
                $productPrototypeDetail->setDesignCodeProductDetail(null);
            }
        }

        return $this;
    }
}
