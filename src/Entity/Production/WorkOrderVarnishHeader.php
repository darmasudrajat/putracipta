<?php

namespace App\Entity\Production;

use App\Entity\Master\Employee;
use App\Entity\ProductionHeader;
use App\Repository\Production\WorkOrderVarnishHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderVarnishHeaderRepository::class)]
#[ORM\Table(name: 'production_work_order_varnish_header')]
class WorkOrderVarnishHeader extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'WOV';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $uvOilQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $alcoholQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $wbQuantity = '0.00';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $workOrderReturnDate = null;

    #[ORM\ManyToOne]
    private ?Employee $employeeIdWorkOrderReturn = null;

    #[ORM\OneToMany(mappedBy: 'workOrderVarnishHeader', targetEntity: WorkOrderVarnishSettingDetail::class)]
    private Collection $workOrderVarnishSettingDetails;

    #[ORM\OneToMany(mappedBy: 'workOrderVarnishHeader', targetEntity: WorkOrderVarnishProductionDetail::class)]
    private Collection $workOrderVarnishProductionDetails;

    #[ORM\ManyToOne(inversedBy: 'workOrderVarnishHeaders')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    public function __construct()
    {
        $this->workOrderVarnishSettingDetails = new ArrayCollection();
        $this->workOrderVarnishProductionDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUvOilQuantity(): ?string
    {
        return $this->uvOilQuantity;
    }

    public function setUvOilQuantity(string $uvOilQuantity): self
    {
        $this->uvOilQuantity = $uvOilQuantity;

        return $this;
    }

    public function getAlcoholQuantity(): ?string
    {
        return $this->alcoholQuantity;
    }

    public function setAlcoholQuantity(string $alcoholQuantity): self
    {
        $this->alcoholQuantity = $alcoholQuantity;

        return $this;
    }

    public function getWbQuantity(): ?string
    {
        return $this->wbQuantity;
    }

    public function setWbQuantity(string $wbQuantity): self
    {
        $this->wbQuantity = $wbQuantity;

        return $this;
    }

    public function getWorkOrderReturnDate(): ?\DateTimeInterface
    {
        return $this->workOrderReturnDate;
    }

    public function setWorkOrderReturnDate(?\DateTimeInterface $workOrderReturnDate): self
    {
        $this->workOrderReturnDate = $workOrderReturnDate;

        return $this;
    }

    public function getEmployeeIdWorkOrderReturn(): ?Employee
    {
        return $this->employeeIdWorkOrderReturn;
    }

    public function setEmployeeIdWorkOrderReturn(?Employee $employeeIdWorkOrderReturn): self
    {
        $this->employeeIdWorkOrderReturn = $employeeIdWorkOrderReturn;

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderVarnishSettingDetail>
     */
    public function getWorkOrderVarnishSettingDetails(): Collection
    {
        return $this->workOrderVarnishSettingDetails;
    }

    public function addWorkOrderVarnishSettingDetail(WorkOrderVarnishSettingDetail $workOrderVarnishSettingDetail): self
    {
        if (!$this->workOrderVarnishSettingDetails->contains($workOrderVarnishSettingDetail)) {
            $this->workOrderVarnishSettingDetails->add($workOrderVarnishSettingDetail);
            $workOrderVarnishSettingDetail->setWorkOrderVarnishHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderVarnishSettingDetail(WorkOrderVarnishSettingDetail $workOrderVarnishSettingDetail): self
    {
        if ($this->workOrderVarnishSettingDetails->removeElement($workOrderVarnishSettingDetail)) {
            // set the owning side to null (unless already changed)
            if ($workOrderVarnishSettingDetail->getWorkOrderVarnishHeader() === $this) {
                $workOrderVarnishSettingDetail->setWorkOrderVarnishHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderVarnishProductionDetail>
     */
    public function getWorkOrderVarnishProductionDetails(): Collection
    {
        return $this->workOrderVarnishProductionDetails;
    }

    public function addWorkOrderVarnishProductionDetail(WorkOrderVarnishProductionDetail $workOrderVarnishProductionDetail): self
    {
        if (!$this->workOrderVarnishProductionDetails->contains($workOrderVarnishProductionDetail)) {
            $this->workOrderVarnishProductionDetails->add($workOrderVarnishProductionDetail);
            $workOrderVarnishProductionDetail->setWorkOrderVarnishHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderVarnishProductionDetail(WorkOrderVarnishProductionDetail $workOrderVarnishProductionDetail): self
    {
        if ($this->workOrderVarnishProductionDetails->removeElement($workOrderVarnishProductionDetail)) {
            // set the owning side to null (unless already changed)
            if ($workOrderVarnishProductionDetail->getWorkOrderVarnishHeader() === $this) {
                $workOrderVarnishProductionDetail->setWorkOrderVarnishHeader(null);
            }
        }

        return $this;
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
}
