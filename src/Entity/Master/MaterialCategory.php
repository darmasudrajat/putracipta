<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\MaterialCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaterialCategoryRepository::class)]
#[ORM\Table(name: 'master_material_category')]
#[UniqueEntity(['name'])]
class MaterialCategory extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'materialCategory', targetEntity: MaterialSubCategory::class)]
    private Collection $materialSubCategories;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $isPaper = false;

    #[ORM\Column(length: 20)]
    private ?string $code = '';

    public function __construct()
    {
        $this->materialSubCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, MaterialSubCategory>
     */
    public function getMaterialSubCategories(): Collection
    {
        return $this->materialSubCategories;
    }

    public function addMaterialSubCategory(MaterialSubCategory $materialSubCategory): self
    {
        if (!$this->materialSubCategories->contains($materialSubCategory)) {
            $this->materialSubCategories->add($materialSubCategory);
            $materialSubCategory->setMaterialCategory($this);
        }

        return $this;
    }

    public function removeMaterialSubCategory(MaterialSubCategory $materialSubCategory): self
    {
        if ($this->materialSubCategories->removeElement($materialSubCategory)) {
            // set the owning side to null (unless already changed)
            if ($materialSubCategory->getMaterialCategory() === $this) {
                $materialSubCategory->setMaterialCategory(null);
            }
        }

        return $this;
    }

    public function isIsPaper(): ?bool
    {
        return $this->isPaper;
    }

    public function setIsPaper(bool $isPaper): self
    {
        $this->isPaper = $isPaper;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
