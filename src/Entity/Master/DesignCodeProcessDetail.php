<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\DesignCodeProcessDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DesignCodeProcessDetailRepository::class)]
#[ORM\Table(name: 'master_design_code_process_detail')]
class DesignCodeProcessDetail extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'designCodeProcessDetails')]
    private ?DesignCode $designCode = null;

    #[ORM\ManyToOne(inversedBy: 'designCodeProcessDetails')]
    private ?WorkOrderProcess $workOrderProcess = null;

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

    public function getWorkOrderProcess(): ?WorkOrderProcess
    {
        return $this->workOrderProcess;
    }

    public function setWorkOrderProcess(?WorkOrderProcess $workOrderProcess): self
    {
        $this->workOrderProcess = $workOrderProcess;

        return $this;
    }
}
