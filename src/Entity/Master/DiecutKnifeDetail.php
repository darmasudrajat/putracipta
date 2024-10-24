<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\DiecutKnifeDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiecutKnifeDetailRepository::class)]
#[ORM\Table(name: 'master_diecut_knife_detail')]
class DiecutKnifeDetail extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'diecutKnifeDetails')]
    #[Assert\NotNull]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'diecutKnifeDetails')]
    #[Assert\NotNull]
    private ?DiecutKnife $diecutKnife = null;

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

    public function getDiecutKnife(): ?DiecutKnife
    {
        return $this->diecutKnife;
    }

    public function setDiecutKnife(?DiecutKnife $diecutKnife): self
    {
        $this->diecutKnife = $diecutKnife;

        return $this;
    }
}
