<?php

namespace App\Entity\Production;

use App\Entity\Master\DesignCodeCheckSheetDetail;
use App\Entity\Master\WorkOrderCheckSheet;
use App\Entity\ProductionDetail;
use App\Repository\Production\MasterOrderCheckSheetDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MasterOrderCheckSheetDetailRepository::class)]
#[ORM\Table(name: 'production_master_order_check_sheet_detail')]
class MasterOrderCheckSheetDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderCheckSheetDetails')]
    #[Assert\NotNull]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\Column]
    private ?bool $isSubcon = false;

    #[ORM\ManyToOne]
    private ?DesignCodeCheckSheetDetail $designCodeCheckSheetDetail = null;

    #[ORM\ManyToOne]
    private ?WorkOrderCheckSheet $workOrderCheckSheet = null;

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

    public function isIsSubcon(): ?bool
    {
        return $this->isSubcon;
    }

    public function setIsSubcon(bool $isSubcon): self
    {
        $this->isSubcon = $isSubcon;

        return $this;
    }

    public function getDesignCodeCheckSheetDetail(): ?DesignCodeCheckSheetDetail
    {
        return $this->designCodeCheckSheetDetail;
    }

    public function setDesignCodeCheckSheetDetail(?DesignCodeCheckSheetDetail $designCodeCheckSheetDetail): self
    {
        $this->designCodeCheckSheetDetail = $designCodeCheckSheetDetail;

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
