<?php

namespace App\Entity\Production;

use App\Entity\Master\Employee;
use App\Entity\ProductionDetail;
use App\Repository\Production\WorkOrderVarnishSpotProductionDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderVarnishSpotProductionDetailRepository::class)]
#[ORM\Table(name: 'production_work_order_varnish_spot_production_detail')]
class WorkOrderVarnishSpotProductionDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $shiftNumber = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $productionDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $productionStartTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $productionEndTime = null;

    #[ORM\Column]
    private ?int $productionOutputQuantity = 0;

    #[ORM\Column]
    private ?int $productionRejectQuantity = 0;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Employee $employeeIdOperator = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $opvUsageQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $uvUsageQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $alcoholUsageQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $wbUsageQuantity = '0.00';

    #[ORM\ManyToOne(inversedBy: 'workOrderVarnishSpotProductionDetails')]
    private ?WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->workOrderVarnishSpotHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProductionStartTime(): ?\DateTimeInterface
    {
        return $this->productionStartTime;
    }

    public function setProductionStartTime(?\DateTimeInterface $productionStartTime): self
    {
        $this->productionStartTime = $productionStartTime;

        return $this;
    }

    public function getProductionEndTime(): ?\DateTimeInterface
    {
        return $this->productionEndTime;
    }

    public function setProductionEndTime(?\DateTimeInterface $productionEndTime): self
    {
        $this->productionEndTime = $productionEndTime;

        return $this;
    }

    public function getProductionOutputQuantity(): ?int
    {
        return $this->productionOutputQuantity;
    }

    public function setProductionOutputQuantity(int $productionOutputQuantity): self
    {
        $this->productionOutputQuantity = $productionOutputQuantity;

        return $this;
    }

    public function getProductionRejectQuantity(): ?int
    {
        return $this->productionRejectQuantity;
    }

    public function setProductionRejectQuantity(int $productionRejectQuantity): self
    {
        $this->productionRejectQuantity = $productionRejectQuantity;

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

    public function getOpvUsageQuantity(): ?string
    {
        return $this->opvUsageQuantity;
    }

    public function setOpvUsageQuantity(string $opvUsageQuantity): self
    {
        $this->opvUsageQuantity = $opvUsageQuantity;

        return $this;
    }

    public function getUvUsageQuantity(): ?string
    {
        return $this->uvUsageQuantity;
    }

    public function setUvUsageQuantity(string $uvUsageQuantity): self
    {
        $this->uvUsageQuantity = $uvUsageQuantity;

        return $this;
    }

    public function getAlcoholUsageQuantity(): ?string
    {
        return $this->alcoholUsageQuantity;
    }

    public function setAlcoholUsageQuantity(string $alcoholUsageQuantity): self
    {
        $this->alcoholUsageQuantity = $alcoholUsageQuantity;

        return $this;
    }

    public function getWbUsageQuantity(): ?string
    {
        return $this->wbUsageQuantity;
    }

    public function setWbUsageQuantity(string $wbUsageQuantity): self
    {
        $this->wbUsageQuantity = $wbUsageQuantity;

        return $this;
    }

    public function getWorkOrderVarnishSpotHeader(): ?WorkOrderVarnishSpotHeader
    {
        return $this->workOrderVarnishSpotHeader;
    }

    public function setWorkOrderVarnishSpotHeader(?WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader): self
    {
        $this->workOrderVarnishSpotHeader = $workOrderVarnishSpotHeader;

        return $this;
    }
}
