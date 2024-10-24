<?php

namespace App\Entity\Production;

use App\Entity\Master\DesignCodeProcessDetail;
use App\Entity\Master\WorkOrderProcess;
use App\Entity\ProductionDetail;
use App\Repository\Production\MasterOrderProcessDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MasterOrderProcessDetailRepository::class)]
#[ORM\Table(name: 'production_master_order_process_detail')]
class MasterOrderProcessDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderProcessDetails')]
    #[Assert\NotNull]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\ManyToOne]
    private ?WorkOrderProcess $workOrderProcess = null;

    #[ORM\Column]
    private ?bool $isSubcon = false;

    #[ORM\ManyToOne]
    private ?DesignCodeProcessDetail $designCodeProcessDetail = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMasterOrderHeader(): ?MasterOrderHeader
    {
        return $this->masterOrderHeader;
    }

    public function setMasterOrderHeader(?MasterOrderHeader $masterOrderHeader): self
    {
        $this->masterOrderHeader = $masterOrderHeader;

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

    public function isIsSubcon(): ?bool
    {
        return $this->isSubcon;
    }

    public function setIsSubcon(bool $isSubcon): self
    {
        $this->isSubcon = $isSubcon;

        return $this;
    }

    public function getDesignCodeProcessDetail(): ?DesignCodeProcessDetail
    {
        return $this->designCodeProcessDetail;
    }

    public function setDesignCodeProcessDetail(?DesignCodeProcessDetail $designCodeProcessDetail): self
    {
        $this->designCodeProcessDetail = $designCodeProcessDetail;

        return $this;
    }
}
