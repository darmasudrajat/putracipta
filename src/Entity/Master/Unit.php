<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\UnitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UnitRepository::class)]
#[ORM\Table(name: 'master_unit')]
class Unit extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'unit', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\OneToMany(mappedBy: 'unit', targetEntity: Paper::class)]
    private Collection $papers;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->papers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setUnit($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUnit() === $this) {
                $product->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Paper>
     */
    public function getPapers(): Collection
    {
        return $this->papers;
    }

    public function addPaper(Paper $paper): self
    {
        if (!$this->papers->contains($paper)) {
            $this->papers->add($paper);
            $paper->setUnit($this);
        }

        return $this;
    }

    public function removePaper(Paper $paper): self
    {
        if ($this->papers->removeElement($paper)) {
            // set the owning side to null (unless already changed)
            if ($paper->getUnit() === $this) {
                $paper->setUnit(null);
            }
        }

        return $this;
    }
}
