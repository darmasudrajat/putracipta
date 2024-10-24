<?php

namespace App\Entity;

use App\Repository\HasilCetakRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HasilCetakRepository::class)]
class HasilCetak
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $tanggal = null;

    #[ORM\Column(length: 255)]
    private ?string $operator = null;

    #[ORM\Column(length: 255)]
    private ?string $good = null;

    #[ORM\Column(length: 255)]
    private ?string $ng = null;

    #[ORM\Column(length: 255)]
    private ?string $mesin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomo(): ?string
    {
        return $this->nomo;
    }

    public function setNomo(string $nomo): static
    {
        $this->nomo = $nomo;

        return $this;
    }

    public function getTanggal(): ?\DateTimeInterface
    {
        return $this->tanggal;
    }

    public function setTanggal(\DateTimeInterface $tanggal): static
    {
        $this->tanggal = $tanggal;

        return $this;
    }

    public function getOperator(): ?string
    {
        return $this->operator;
    }

    public function setOperator(string $operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    public function getGood(): ?string
    {
        return $this->good;
    }

    public function setGood(string $good): static
    {
        $this->good = $good;

        return $this;
    }

    public function getNg(): ?string
    {
        return $this->ng;
    }

    public function setNg(string $ng): static
    {
        $this->ng = $ng;

        return $this;
    }

    public function getMesin(): ?string
    {
        return $this->mesin;
    }

    public function setMesin(string $mesin): static
    {
        $this->mesin = $mesin;

        return $this;
    }
}
