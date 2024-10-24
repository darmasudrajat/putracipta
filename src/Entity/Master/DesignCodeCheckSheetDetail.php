<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\DesignCodeCheckSheetDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DesignCodeCheckSheetDetailRepository::class)]
#[ORM\Table(name: 'master_design_code_check_sheet_detail')]
class DesignCodeCheckSheetDetail extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'designCodeCheckSheetDetails')]
    private ?DesignCode $designCode = null;

    #[ORM\ManyToOne(inversedBy: 'designCodeCheckSheetDetails')]
    private ?WorkOrderCheckSheet $workOrderCheckSheet = null;

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

    public function getWorkOrderCheckSheet(): ?WorkOrderCheckSheet
    {
        return $this->workOrderCheckSheet;
    }

    public function setWorkOrderCheckSheet(?WorkOrderCheckSheet $workOrderCheckSheet): self
    {
        $this->workOrderCheckSheet = $workOrderCheckSheet;

        return $this;
    }
}
