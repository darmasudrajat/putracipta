<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\DielineMillarDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DielineMillarDetailRepository::class)]
#[ORM\Table(name: 'master_dieline_millar_detail')]
class DielineMillarDetail extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dielineMillarDetails')]
    #[Assert\NotNull]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'dielineMillarDetails')]
    #[Assert\NotNull]
    private ?DielineMillar $dielineMillar = null;

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

    public function getDielineMillar(): ?DielineMillar
    {
        return $this->dielineMillar;
    }

    public function setDielineMillar(?DielineMillar $dielineMillar): self
    {
        $this->dielineMillar = $dielineMillar;

        return $this;
    }
}
