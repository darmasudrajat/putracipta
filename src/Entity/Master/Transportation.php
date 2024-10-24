<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\TransportationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransportationRepository::class)]
#[ORM\Table(name: 'master_transportation')]
class Transportation extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $plateNumber = '';

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getNameAndPlateNumber() {
        return $this->name . ' (' . $this->plateNumber . ')';
    }

    public function getPlateNumber(): ?string
    {
        return $this->plateNumber;
    }

    public function setPlateNumber(string $plateNumber): self
    {
        $this->plateNumber = $plateNumber;

        return $this;
    }
}
