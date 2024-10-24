<?php

namespace App\Entity\Production;

use App\Entity\Master\Employee;
use App\Entity\ProductionHeader;
use App\Repository\Production\WorkOrderVarnishSpotHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderVarnishSpotHeaderRepository::class)]
#[ORM\Table(name: 'production_work_order_varnish_spot_header')]
class WorkOrderVarnishSpotHeader extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'WOVS';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $varnishType = '';

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

    #[ORM\OneToMany(mappedBy: 'workOrderVarnishSpotHeader', targetEntity: WorkOrderVarnishSpotSettingDetail::class)]
    private Collection $workOrderVarnishSpotSettingDetails;

    #[ORM\OneToMany(mappedBy: 'workOrderVarnishSpotHeader', targetEntity: WorkOrderVarnishSpotProductionDetail::class)]
    private Collection $workOrderVarnishSpotProductionDetails;

    #[ORM\ManyToOne(inversedBy: 'workOrderVarnishSpotHeaders')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    public function __construct()
    {
        $this->workOrderVarnishSpotSettingDetails = new ArrayCollection();
        $this->workOrderVarnishSpotProductionDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVarnishType(): ?string
    {
        return $this->varnishType;
    }

    public function setVarnishType(string $varnishType): self
    {
        $this->varnishType = $varnishType;

        return $this;
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
     * @return Collection<int, WorkOrderVarnishSpotSettingDetail>
     */
    public function getWorkOrderVarnishSpotSettingDetails(): Collection
    {
        return $this->workOrderVarnishSpotSettingDetails;
    }

    public function addWorkOrderVarnishSpotSettingDetail(WorkOrderVarnishSpotSettingDetail $workOrderVarnishSpotSettingDetail): self
    {
        if (!$this->workOrderVarnishSpotSettingDetails->contains($workOrderVarnishSpotSettingDetail)) {
            $this->workOrderVarnishSpotSettingDetails->add($workOrderVarnishSpotSettingDetail);
            $workOrderVarnishSpotSettingDetail->setWorkOrderVarnishSpotHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderVarnishSpotSettingDetail(WorkOrderVarnishSpotSettingDetail $workOrderVarnishSpotSettingDetail): self
    {
        if ($this->workOrderVarnishSpotSettingDetails->removeElement($workOrderVarnishSpotSettingDetail)) {
            // set the owning side to null (unless already changed)
            if ($workOrderVarnishSpotSettingDetail->getWorkOrderVarnishSpotHeader() === $this) {
                $workOrderVarnishSpotSettingDetail->setWorkOrderVarnishSpotHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderVarnishSpotProductionDetail>
     */
    public function getWorkOrderVarnishSpotProductionDetails(): Collection
    {
        return $this->workOrderVarnishSpotProductionDetails;
    }

    public function addWorkOrderVarnishSpotProductionDetail(WorkOrderVarnishSpotProductionDetail $workOrderVarnishSpotProductionDetail): self
    {
        if (!$this->workOrderVarnishSpotProductionDetails->contains($workOrderVarnishSpotProductionDetail)) {
            $this->workOrderVarnishSpotProductionDetails->add($workOrderVarnishSpotProductionDetail);
            $workOrderVarnishSpotProductionDetail->setWorkOrderVarnishSpotHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderVarnishSpotProductionDetail(WorkOrderVarnishSpotProductionDetail $workOrderVarnishSpotProductionDetail): self
    {
        if ($this->workOrderVarnishSpotProductionDetails->removeElement($workOrderVarnishSpotProductionDetail)) {
            // set the owning side to null (unless already changed)
            if ($workOrderVarnishSpotProductionDetail->getWorkOrderVarnishSpotHeader() === $this) {
                $workOrderVarnishSpotProductionDetail->setWorkOrderVarnishSpotHeader(null);
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
