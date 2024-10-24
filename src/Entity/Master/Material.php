<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\MaterialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
#[ORM\Table(name: 'master_material')]
#[UniqueEntity(['name'])]
class Material extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
//    #[Assert\NotBlank]
    private ?string $code = '';

    #[ORM\ManyToOne(inversedBy: 'materials')]
    #[Assert\NotNull]
    private ?MaterialSubCategory $materialSubCategory = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $thickness = '';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Unit $unit = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $variant = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $note = '';

    #[ORM\Column(length: 60)]
    private ?string $density = '';

    #[ORM\Column(length: 60)]
    private ?string $viscosity = '';

    #[ORM\Column]
    private ?int $codeOrdinal = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getCodeNumber(): ?string 
    {
        $materialSubCategory = $this->materialSubCategory;
        return sprintf('%s-%s-%03d', $materialSubCategory->getMaterialCategory()->getCode(), $materialSubCategory->getCode(), $this->codeOrdinal);
    }

    public function setCodeOrdinalToNext($ordinal): self
    {
        $this->codeOrdinal = $ordinal + 1;

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

    public function getMaterialSubCategory(): ?MaterialSubCategory
    {
        return $this->materialSubCategory;
    }

    public function setMaterialSubCategory(?MaterialSubCategory $materialSubCategory): self
    {
        $this->materialSubCategory = $materialSubCategory;

        return $this;
    }

    public function getThickness(): ?string
    {
        return $this->thickness;
    }

    public function setThickness(string $thickness): self
    {
        $this->thickness = $thickness;

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

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getDensity(): ?string
    {
        return $this->density;
    }

    public function setDensity(string $density): self
    {
        $this->density = $density;

        return $this;
    }

    public function getViscosity(): ?string
    {
        return $this->viscosity;
    }

    public function setViscosity(string $viscosity): self
    {
        $this->viscosity = $viscosity;

        return $this;
    }

    public function getCodeOrdinal(): ?int
    {
        return $this->codeOrdinal;
    }

    public function setCodeOrdinal(int $codeOrdinal): self
    {
        $this->codeOrdinal = $codeOrdinal;

        return $this;
    }
}
