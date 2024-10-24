<?php

namespace App\Entity\Production;

use App\Entity\Master\Employee;
use App\Entity\ProductionHeader;
use App\Repository\Production\WorkOrderCuttingHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderCuttingHeaderRepository::class)]
#[ORM\Table(name: 'production_work_order_cutting_header')]
class WorkOrderCuttingHeader extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'WOC';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isSizeFit = false;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $workOrderReturnDate = null;

    #[ORM\ManyToOne]
    private ?Employee $employeeIdWorkOrderReturn = null;

    #[ORM\OneToMany(mappedBy: 'workOrderCuttingHeader', targetEntity: WorkOrderCuttingMaterialDetail::class)]
    private Collection $workOrderCuttingMaterialDetails;

    #[ORM\OneToMany(mappedBy: 'workOrderCuttingHeader', targetEntity: WorkOrderCuttingFinishedDetail::class)]
    private Collection $workOrderCuttingFinishedDetails;

    #[ORM\ManyToOne(inversedBy: 'workOrderCuttingHeaders')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    public function __construct()
    {
        $this->workOrderCuttingMaterialDetails = new ArrayCollection();
        $this->workOrderCuttingFinishedDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsSizeFit(): ?bool
    {
        return $this->isSizeFit;
    }

    public function setIsSizeFit(bool $isSizeFit): self
    {
        $this->isSizeFit = $isSizeFit;

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
     * @return Collection<int, WorkOrderCuttingMaterialDetail>
     */
    public function getWorkOrderCuttingMaterialDetails(): Collection
    {
        return $this->workOrderCuttingMaterialDetails;
    }

    public function addWorkOrderCuttingMaterialDetail(WorkOrderCuttingMaterialDetail $workOrderCuttingMaterialDetail): self
    {
        if (!$this->workOrderCuttingMaterialDetails->contains($workOrderCuttingMaterialDetail)) {
            $this->workOrderCuttingMaterialDetails->add($workOrderCuttingMaterialDetail);
            $workOrderCuttingMaterialDetail->setWorkOrderCuttingHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderCuttingMaterialDetail(WorkOrderCuttingMaterialDetail $workOrderCuttingMaterialDetail): self
    {
        if ($this->workOrderCuttingMaterialDetails->removeElement($workOrderCuttingMaterialDetail)) {
            // set the owning side to null (unless already changed)
            if ($workOrderCuttingMaterialDetail->getWorkOrderCuttingHeader() === $this) {
                $workOrderCuttingMaterialDetail->setWorkOrderCuttingHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderCuttingFinishedDetail>
     */
    public function getWorkOrderCuttingFinishedDetails(): Collection
    {
        return $this->workOrderCuttingFinishedDetails;
    }

    public function addWorkOrderCuttingFinishedDetail(WorkOrderCuttingFinishedDetail $workOrderCuttingFinishedDetail): self
    {
        if (!$this->workOrderCuttingFinishedDetails->contains($workOrderCuttingFinishedDetail)) {
            $this->workOrderCuttingFinishedDetails->add($workOrderCuttingFinishedDetail);
            $workOrderCuttingFinishedDetail->setWorkOrderCuttingHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderCuttingFinishedDetail(WorkOrderCuttingFinishedDetail $workOrderCuttingFinishedDetail): self
    {
        if ($this->workOrderCuttingFinishedDetails->removeElement($workOrderCuttingFinishedDetail)) {
            // set the owning side to null (unless already changed)
            if ($workOrderCuttingFinishedDetail->getWorkOrderCuttingHeader() === $this) {
                $workOrderCuttingFinishedDetail->setWorkOrderCuttingHeader(null);
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
