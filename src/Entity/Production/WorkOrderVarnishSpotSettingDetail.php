<?php

namespace App\Entity\Production;

use App\Entity\Master\Employee;
use App\Entity\ProductionDetail;
use App\Repository\Production\WorkOrderVarnishSpotSettingDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderVarnishSpotSettingDetailRepository::class)]
#[ORM\Table(name: 'production_work_order_varnish_spot_setting_detail')]
class WorkOrderVarnishSpotSettingDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $shiftNumber = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $settingDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $settingStartTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $settingEndTime = null;

    #[ORM\ManyToOne]
    private ?Employee $employeeIdOperator = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne(inversedBy: 'workOrderVarnishSpotSettingDetails')]
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

    public function getSettingDate(): ?\DateTimeInterface
    {
        return $this->settingDate;
    }

    public function setSettingDate(?\DateTimeInterface $settingDate): self
    {
        $this->settingDate = $settingDate;

        return $this;
    }

    public function getSettingStartTime(): ?\DateTimeInterface
    {
        return $this->settingStartTime;
    }

    public function setSettingStartTime(?\DateTimeInterface $settingStartTime): self
    {
        $this->settingStartTime = $settingStartTime;

        return $this;
    }

    public function getSettingEndTime(): ?\DateTimeInterface
    {
        return $this->settingEndTime;
    }

    public function setSettingEndTime(?\DateTimeInterface $settingEndTime): self
    {
        $this->settingEndTime = $settingEndTime;

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

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

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
