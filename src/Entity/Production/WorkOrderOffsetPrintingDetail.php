<?php

namespace App\Entity\Production;

use App\Entity\ProductionDetail;
use App\Entity\Master\Employee;
use App\Repository\Production\WorkOrderOffsetPrintingDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderOffsetPrintingDetailRepository::class)]
#[ORM\Table(name: 'production_work_order_offset_printing_detail')]
class WorkOrderOffsetPrintingDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workOrderOffsetPrintingDetails')]
    private ?WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader = null;

    #[ORM\Column]
    private ?int $shiftNumber = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $productionDate = null;

    #[ORM\Column(length: 60)]
    private ?string $productionColor = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $productionStartTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $productionEndTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $productionOutputStartTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $productionOutputEndTime = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = null;

    #[ORM\ManyToOne]
    private ?Employee $employeeIdOperator = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->workOrderOffsetPrintingHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkOrderOffsetPrintingHeader(): ?WorkOrderOffsetPrintingHeader
    {
        return $this->workOrderOffsetPrintingHeader;
    }

    public function setWorkOrderOffsetPrintingHeader(?WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader): self
    {
        $this->workOrderOffsetPrintingHeader = $workOrderOffsetPrintingHeader;

        return $this;
    }

    public function getShiftNumber(): ?int
    {
        return $this->shiftNumber;
    }

    public function setShiftNumber(int $shiftNumber): self
    {
        $this->shiftNumber = $shiftNumber;

        return $this;
    }

    public function getProductionDate(): ?\DateTimeInterface
    {
        return $this->productionDate;
    }

    public function setProductionDate(?\DateTimeInterface $productionDate): self
    {
        $this->productionDate = $productionDate;

        return $this;
    }

    public function getProductionColor(): ?string
    {
        return $this->productionColor;
    }

    public function setProductionColor(string $productionColor): self
    {
        $this->productionColor = $productionColor;

        return $this;
    }

    public function getProductionStartTime(): ?\DateTimeInterface
    {
        return $this->productionStartTime;
    }

    public function setProductionStartTime(\DateTimeInterface $productionStartTime): self
    {
        $this->productionStartTime = $productionStartTime;

        return $this;
    }

    public function getProductionEndTime(): ?\DateTimeInterface
    {
        return $this->productionEndTime;
    }

    public function setProductionEndTime(\DateTimeInterface $productionEndTime): self
    {
        $this->productionEndTime = $productionEndTime;

        return $this;
    }

    public function getProductionOutputStartTime(): ?\DateTimeInterface
    {
        return $this->productionOutputStartTime;
    }

    public function setProductionOutputStartTime(\DateTimeInterface $productionOutputStartTime): self
    {
        $this->productionOutputStartTime = $productionOutputStartTime;

        return $this;
    }

    public function getProductionOutputEndTime(): ?\DateTimeInterface
    {
        return $this->productionOutputEndTime;
    }

    public function setProductionOutputEndTime(\DateTimeInterface $productionOutputEndTime): self
    {
        $this->productionOutputEndTime = $productionOutputEndTime;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }

    public function getEmployeeIdOperator(): ?Employee
    {
        return $this->employeeIdOperator;
    }

    public function setEmployeeIdOperator(?Employee $employeeIdOperator): self
    {
        $this->employeeIdOperator = $employeeIdOperator;

        return $this;
    }
}
