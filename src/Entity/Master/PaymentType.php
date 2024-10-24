<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\PaymentTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentTypeRepository::class)]
#[ORM\Table(name: 'master_payment_type')]
class PaymentType extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
