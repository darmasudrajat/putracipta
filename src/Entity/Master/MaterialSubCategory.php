<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\MaterialSubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaterialSubCategoryRepository::class)]
#[ORM\Table(name: 'master_material_sub_category')]
#[UniqueEntity(['name'])]
class MaterialSubCategory extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'materialSubCategories')]
    #[Assert\NotNull]
    private ?MaterialCategory $materialCategory = null;

    #[ORM\OneToMany(mappedBy: 'materialSubCategory', targetEntity: Material::class)]
    private Collection $materials;

    #[ORM\OneToMany(mappedBy: 'materialSubCategory', targetEntity: Paper::class)]
    private Collection $papers;

    #[ORM\Column(length: 20)]
    private ?string $code = '';

    public function __construct()
    {
        $this->materials = new ArrayCollection();
        $this->papers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaterialCategory(): ?MaterialCategory
    {
        return $this->materialCategory;
    }

    public function setMaterialCategory(?MaterialCategory $materialCategory): self
    {
        $this->materialCategory = $materialCategory;

        return $this;
    }

    /**
     * @return Collection<int, Material>
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(Material $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials->add($material);
            $material->setMaterialSubCategory($this);
        }

        return $this;
    }

    public function removeMaterial(Material $material): self
    {
        if ($this->materials->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getMaterialSubCategory() === $this) {
                $material->setMaterialSubCategory(null);
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
            $paper->setMaterialSubCategory($this);
        }

        return $this;
    }

    public function removePaper(Paper $paper): self
    {
        if ($this->papers->removeElement($paper)) {
            // set the owning side to null (unless already changed)
            if ($paper->getMaterialSubCategory() === $this) {
                $paper->setMaterialSubCategory(null);
            }
        }

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
