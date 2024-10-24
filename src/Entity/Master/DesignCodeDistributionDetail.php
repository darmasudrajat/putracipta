<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\DesignCodeDistributionDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DesignCodeDistributionDetailRepository::class)]
#[ORM\Table(name: 'master_design_code_distribution_detail')]
class DesignCodeDistributionDetail extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'designCodeDistributionDetails')]
    private ?DesignCode $designCode = null;

    #[ORM\ManyToOne(inversedBy: 'designCodeDistributionDetails')]
    private ?WorkOrderDistribution $workOrderDistribution = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWorkOrderDistribution(): ?WorkOrderDistribution
    {
        return $this->workOrderDistribution;
    }

    public function setWorkOrderDistribution(?WorkOrderDistribution $workOrderDistribution): self
    {
        $this->workOrderDistribution = $workOrderDistribution;

        return $this;
    }
}
