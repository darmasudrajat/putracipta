<?php

namespace App\Entity\Production;

use App\Entity\Master\Employee;
use App\Entity\ProductionHeader;
use App\Repository\Production\WorkOrderPrepressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderPrepressRepository::class)]
#[ORM\Table(name: 'production_work_order_prepress')]
class WorkOrderPrepress extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'WOP';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantityPlateNew = 0;

    #[ORM\Column]
    private ?int $quantityPlateOld = 0;

    #[ORM\Column]
    private ?int $quantityPlateUsed = 0;

    #[ORM\Column(length: 60)]
    private ?string $plateBrand = '';

    #[ORM\ManyToOne]
    private ?Employee $employeeIdPlateRelease = null;

    #[ORM\ManyToOne]
    private ?Employee $employeeIdWorkOrderReturn = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $workOrderReturnDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalCyanBeginningOutputStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalCyanBeginningOutputEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalMagentaBeginningOutputStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalMagentaBeginningOutputEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalYellowBeginningOutputStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalYellowBeginningOutputEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalBlackBeginningOutputStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalBlackBeginningOutputEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalCyanRevisionStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalCyanRevisionEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalMagentaRevisionStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalMagentaRevisionEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalYellowRevisionStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalYellowRevisionEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalBlackRevisionStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalBlackRevisionEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalCyanDowntimeStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalCyanDowntimeEndDate = null;

    #[ORM\Column(length: 100)]
    private ?string $internalCyanDowntimeMemo = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalMagentaDowntimeStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalMagentaDowntimeEndDate = null;

    #[ORM\Column(length: 100)]
    private ?string $internalMagentaDowntimeMemo = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalYellowDowntimeStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalYellowDowntimeEndDate = null;

    #[ORM\Column(length: 100)]
    private ?string $internalYellowDowntimeMemo = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalBlackDowntimeStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $internalBlackDowntimeEndDate = null;

    #[ORM\Column(length: 100)]
    private ?string $internalBlackDowntimeMemo = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconCyanBeginningOutputStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconCyanBeginningOutputEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconMagentaBeginningOutputStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconMagentaBeginningOutputEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconYellowBeginningOutputStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconYellowBeginningOutputEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconBlackBeginningOutputStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconBlackBeginningOutputEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconCyanRevisionStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconCyanRevisionEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconMagentaRevisionStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconMagentaRevisionEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconYellowRevisionStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconYellowRevisionEndDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconBlackRevisionStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subconBlackRevisionEndDate = null;

    #[ORM\ManyToOne(inversedBy: 'workOrderPrepresses')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantityPlateNew(): ?int
    {
        return $this->quantityPlateNew;
    }

    public function setQuantityPlateNew(int $quantityPlateNew): self
    {
        $this->quantityPlateNew = $quantityPlateNew;

        return $this;
    }

    public function getQuantityPlateOld(): ?int
    {
        return $this->quantityPlateOld;
    }

    public function setQuantityPlateOld(int $quantityPlateOld): self
    {
        $this->quantityPlateOld = $quantityPlateOld;

        return $this;
    }

    public function getQuantityPlateUsed(): ?int
    {
        return $this->quantityPlateUsed;
    }

    public function setQuantityPlateUsed(int $quantityPlateUsed): self
    {
        $this->quantityPlateUsed = $quantityPlateUsed;

        return $this;
    }

    public function getPlateBrand(): ?string
    {
        return $this->plateBrand;
    }

    public function setPlateBrand(string $plateBrand): self
    {
        $this->plateBrand = $plateBrand;

        return $this;
    }

    public function getEmployeeIdPlateRelease(): ?Employee
    {
        return $this->employeeIdPlateRelease;
    }

    public function setEmployeeIdPlateRelease(?Employee $employeeIdPlateRelease): self
    {
        $this->employeeIdPlateRelease = $employeeIdPlateRelease;

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

    public function getWorkOrderReturnDate(): ?\DateTimeInterface
    {
        return $this->workOrderReturnDate;
    }

    public function setWorkOrderReturnDate(?\DateTimeInterface $workOrderReturnDate): self
    {
        $this->workOrderReturnDate = $workOrderReturnDate;

        return $this;
    }

    public function getInternalCyanBeginningOutputStartDate(): ?\DateTimeInterface
    {
        return $this->internalCyanBeginningOutputStartDate;
    }

    public function setInternalCyanBeginningOutputStartDate(?\DateTimeInterface $internalCyanBeginningOutputStartDate): self
    {
        $this->internalCyanBeginningOutputStartDate = $internalCyanBeginningOutputStartDate;

        return $this;
    }

    public function getInternalCyanBeginningOutputEndDate(): ?\DateTimeInterface
    {
        return $this->internalCyanBeginningOutputEndDate;
    }

    public function setInternalCyanBeginningOutputEndDate(?\DateTimeInterface $internalCyanBeginningOutputEndDate): self
    {
        $this->internalCyanBeginningOutputEndDate = $internalCyanBeginningOutputEndDate;

        return $this;
    }

    public function getInternalMagentaBeginningOutputStartDate(): ?\DateTimeInterface
    {
        return $this->internalMagentaBeginningOutputStartDate;
    }

    public function setInternalMagentaBeginningOutputStartDate(?\DateTimeInterface $internalMagentaBeginningOutputStartDate): self
    {
        $this->internalMagentaBeginningOutputStartDate = $internalMagentaBeginningOutputStartDate;

        return $this;
    }

    public function getInternalMagentaBeginningOutputEndDate(): ?\DateTimeInterface
    {
        return $this->internalMagentaBeginningOutputEndDate;
    }

    public function setInternalMagentaBeginningOutputEndDate(?\DateTimeInterface $internalMagentaBeginningOutputEndDate): self
    {
        $this->internalMagentaBeginningOutputEndDate = $internalMagentaBeginningOutputEndDate;

        return $this;
    }

    public function getInternalYellowBeginningOutputStartDate(): ?\DateTimeInterface
    {
        return $this->internalYellowBeginningOutputStartDate;
    }

    public function setInternalYellowBeginningOutputStartDate(?\DateTimeInterface $internalYellowBeginningOutputStartDate): self
    {
        $this->internalYellowBeginningOutputStartDate = $internalYellowBeginningOutputStartDate;

        return $this;
    }

    public function getInternalYellowBeginningOutputEndDate(): ?\DateTimeInterface
    {
        return $this->internalYellowBeginningOutputEndDate;
    }

    public function setInternalYellowBeginningOutputEndDate(?\DateTimeInterface $internalYellowBeginningOutputEndDate): self
    {
        $this->internalYellowBeginningOutputEndDate = $internalYellowBeginningOutputEndDate;

        return $this;
    }

    public function getInternalBlackBeginningOutputStartDate(): ?\DateTimeInterface
    {
        return $this->internalBlackBeginningOutputStartDate;
    }

    public function setInternalBlackBeginningOutputStartDate(?\DateTimeInterface $internalBlackBeginningOutputStartDate): self
    {
        $this->internalBlackBeginningOutputStartDate = $internalBlackBeginningOutputStartDate;

        return $this;
    }

    public function getInternalBlackBeginningOutputEndDate(): ?\DateTimeInterface
    {
        return $this->internalBlackBeginningOutputEndDate;
    }

    public function setInternalBlackBeginningOutputEndDate(?\DateTimeInterface $internalBlackBeginningOutputEndDate): self
    {
        $this->internalBlackBeginningOutputEndDate = $internalBlackBeginningOutputEndDate;

        return $this;
    }

    public function getInternalCyanRevisionStartDate(): ?\DateTimeInterface
    {
        return $this->internalCyanRevisionStartDate;
    }

    public function setInternalCyanRevisionStartDate(?\DateTimeInterface $internalCyanRevisionStartDate): self
    {
        $this->internalCyanRevisionStartDate = $internalCyanRevisionStartDate;

        return $this;
    }

    public function getInternalCyanRevisionEndDate(): ?\DateTimeInterface
    {
        return $this->internalCyanRevisionEndDate;
    }

    public function setInternalCyanRevisionEndDate(?\DateTimeInterface $internalCyanRevisionEndDate): self
    {
        $this->internalCyanRevisionEndDate = $internalCyanRevisionEndDate;

        return $this;
    }

    public function getInternalMagentaRevisionStartDate(): ?\DateTimeInterface
    {
        return $this->internalMagentaRevisionStartDate;
    }

    public function setInternalMagentaRevisionStartDate(?\DateTimeInterface $internalMagentaRevisionStartDate): self
    {
        $this->internalMagentaRevisionStartDate = $internalMagentaRevisionStartDate;

        return $this;
    }

    public function getInternalMagentaRevisionEndDate(): ?\DateTimeInterface
    {
        return $this->internalMagentaRevisionEndDate;
    }

    public function setInternalMagentaRevisionEndDate(?\DateTimeInterface $internalMagentaRevisionEndDate): self
    {
        $this->internalMagentaRevisionEndDate = $internalMagentaRevisionEndDate;

        return $this;
    }

    public function getInternalYellowRevisionStartDate(): ?\DateTimeInterface
    {
        return $this->internalYellowRevisionStartDate;
    }

    public function setInternalYellowRevisionStartDate(?\DateTimeInterface $internalYellowRevisionStartDate): self
    {
        $this->internalYellowRevisionStartDate = $internalYellowRevisionStartDate;

        return $this;
    }

    public function getInternalYellowRevisionEndDate(): ?\DateTimeInterface
    {
        return $this->internalYellowRevisionEndDate;
    }

    public function setInternalYellowRevisionEndDate(?\DateTimeInterface $internalYellowRevisionEndDate): self
    {
        $this->internalYellowRevisionEndDate = $internalYellowRevisionEndDate;

        return $this;
    }

    public function getInternalBlackRevisionStartDate(): ?\DateTimeInterface
    {
        return $this->internalBlackRevisionStartDate;
    }

    public function setInternalBlackRevisionStartDate(?\DateTimeInterface $internalBlackRevisionStartDate): self
    {
        $this->internalBlackRevisionStartDate = $internalBlackRevisionStartDate;

        return $this;
    }

    public function getInternalBlackRevisionEndDate(): ?\DateTimeInterface
    {
        return $this->internalBlackRevisionEndDate;
    }

    public function setInternalBlackRevisionEndDate(?\DateTimeInterface $internalBlackRevisionEndDate): self
    {
        $this->internalBlackRevisionEndDate = $internalBlackRevisionEndDate;

        return $this;
    }

    public function getInternalCyanDowntimeStartDate(): ?\DateTimeInterface
    {
        return $this->internalCyanDowntimeStartDate;
    }

    public function setInternalCyanDowntimeStartDate(?\DateTimeInterface $internalCyanDowntimeStartDate): self
    {
        $this->internalCyanDowntimeStartDate = $internalCyanDowntimeStartDate;

        return $this;
    }

    public function getInternalCyanDowntimeEndDate(): ?\DateTimeInterface
    {
        return $this->internalCyanDowntimeEndDate;
    }

    public function setInternalCyanDowntimeEndDate(?\DateTimeInterface $internalCyanDowntimeEndDate): self
    {
        $this->internalCyanDowntimeEndDate = $internalCyanDowntimeEndDate;

        return $this;
    }

    public function getInternalCyanDowntimeMemo(): ?string
    {
        return $this->internalCyanDowntimeMemo;
    }

    public function setInternalCyanDowntimeMemo(string $internalCyanDowntimeMemo): self
    {
        $this->internalCyanDowntimeMemo = $internalCyanDowntimeMemo;

        return $this;
    }

    public function getInternalMagentaDowntimeStartDate(): ?\DateTimeInterface
    {
        return $this->internalMagentaDowntimeStartDate;
    }

    public function setInternalMagentaDowntimeStartDate(?\DateTimeInterface $internalMagentaDowntimeStartDate): self
    {
        $this->internalMagentaDowntimeStartDate = $internalMagentaDowntimeStartDate;

        return $this;
    }

    public function getInternalMagentaDowntimeEndDate(): ?\DateTimeInterface
    {
        return $this->internalMagentaDowntimeEndDate;
    }

    public function setInternalMagentaDowntimeEndDate(?\DateTimeInterface $internalMagentaDowntimeEndDate): self
    {
        $this->internalMagentaDowntimeEndDate = $internalMagentaDowntimeEndDate;

        return $this;
    }

    public function getInternalMagentaDowntimeMemo(): ?string
    {
        return $this->internalMagentaDowntimeMemo;
    }

    public function setInternalMagentaDowntimeMemo(string $internalMagentaDowntimeMemo): self
    {
        $this->internalMagentaDowntimeMemo = $internalMagentaDowntimeMemo;

        return $this;
    }

    public function getInternalYellowDowntimeStartDate(): ?\DateTimeInterface
    {
        return $this->internalYellowDowntimeStartDate;
    }

    public function setInternalYellowDowntimeStartDate(?\DateTimeInterface $internalYellowDowntimeStartDate): self
    {
        $this->internalYellowDowntimeStartDate = $internalYellowDowntimeStartDate;

        return $this;
    }

    public function getInternalYellowDowntimeEndDate(): ?\DateTimeInterface
    {
        return $this->internalYellowDowntimeEndDate;
    }

    public function setInternalYellowDowntimeEndDate(?\DateTimeInterface $internalYellowDowntimeEndDate): self
    {
        $this->internalYellowDowntimeEndDate = $internalYellowDowntimeEndDate;

        return $this;
    }

    public function getInternalYellowDowntimeMemo(): ?string
    {
        return $this->internalYellowDowntimeMemo;
    }

    public function setInternalYellowDowntimeMemo(string $internalYellowDowntimeMemo): self
    {
        $this->internalYellowDowntimeMemo = $internalYellowDowntimeMemo;

        return $this;
    }

    public function getInternalBlackDowntimeStartDate(): ?\DateTimeInterface
    {
        return $this->internalBlackDowntimeStartDate;
    }

    public function setInternalBlackDowntimeStartDate(?\DateTimeInterface $internalBlackDowntimeStartDate): self
    {
        $this->internalBlackDowntimeStartDate = $internalBlackDowntimeStartDate;

        return $this;
    }

    public function getInternalBlackDowntimeEndDate(): ?\DateTimeInterface
    {
        return $this->internalBlackDowntimeEndDate;
    }

    public function setInternalBlackDowntimeEndDate(?\DateTimeInterface $internalBlackDowntimeEndDate): self
    {
        $this->internalBlackDowntimeEndDate = $internalBlackDowntimeEndDate;

        return $this;
    }

    public function getInternalBlackDowntimeMemo(): ?string
    {
        return $this->internalBlackDowntimeMemo;
    }

    public function setInternalBlackDowntimeMemo(string $internalBlackDowntimeMemo): self
    {
        $this->internalBlackDowntimeMemo = $internalBlackDowntimeMemo;

        return $this;
    }

    public function getSubconCyanBeginningOutputStartDate(): ?\DateTimeInterface
    {
        return $this->subconCyanBeginningOutputStartDate;
    }

    public function setSubconCyanBeginningOutputStartDate(?\DateTimeInterface $subconCyanBeginningOutputStartDate): self
    {
        $this->subconCyanBeginningOutputStartDate = $subconCyanBeginningOutputStartDate;

        return $this;
    }

    public function getSubconCyanBeginningOutputEndDate(): ?\DateTimeInterface
    {
        return $this->subconCyanBeginningOutputEndDate;
    }

    public function setSubconCyanBeginningOutputEndDate(?\DateTimeInterface $subconCyanBeginningOutputEndDate): self
    {
        $this->subconCyanBeginningOutputEndDate = $subconCyanBeginningOutputEndDate;

        return $this;
    }

    public function getSubconMagentaBeginningOutputStartDate(): ?\DateTimeInterface
    {
        return $this->subconMagentaBeginningOutputStartDate;
    }

    public function setSubconMagentaBeginningOutputStartDate(?\DateTimeInterface $subconMagentaBeginningOutputStartDate): self
    {
        $this->subconMagentaBeginningOutputStartDate = $subconMagentaBeginningOutputStartDate;

        return $this;
    }

    public function getSubconMagentaBeginningOutputEndDate(): ?\DateTimeInterface
    {
        return $this->subconMagentaBeginningOutputEndDate;
    }

    public function setSubconMagentaBeginningOutputEndDate(?\DateTimeInterface $subconMagentaBeginningOutputEndDate): self
    {
        $this->subconMagentaBeginningOutputEndDate = $subconMagentaBeginningOutputEndDate;

        return $this;
    }

    public function getSubconYellowBeginningOutputStartDate(): ?\DateTimeInterface
    {
        return $this->subconYellowBeginningOutputStartDate;
    }

    public function setSubconYellowBeginningOutputStartDate(?\DateTimeInterface $subconYellowBeginningOutputStartDate): self
    {
        $this->subconYellowBeginningOutputStartDate = $subconYellowBeginningOutputStartDate;

        return $this;
    }

    public function getSubconYellowBeginningOutputEndDate(): ?\DateTimeInterface
    {
        return $this->subconYellowBeginningOutputEndDate;
    }

    public function setSubconYellowBeginningOutputEndDate(?\DateTimeInterface $subconYellowBeginningOutputEndDate): self
    {
        $this->subconYellowBeginningOutputEndDate = $subconYellowBeginningOutputEndDate;

        return $this;
    }

    public function getSubconBlackBeginningOutputStartDate(): ?\DateTimeInterface
    {
        return $this->subconBlackBeginningOutputStartDate;
    }

    public function setSubconBlackBeginningOutputStartDate(?\DateTimeInterface $subconBlackBeginningOutputStartDate): self
    {
        $this->subconBlackBeginningOutputStartDate = $subconBlackBeginningOutputStartDate;

        return $this;
    }

    public function getSubconBlackBeginningOutputEndDate(): ?\DateTimeInterface
    {
        return $this->subconBlackBeginningOutputEndDate;
    }

    public function setSubconBlackBeginningOutputEndDate(?\DateTimeInterface $subconBlackBeginningOutputEndDate): self
    {
        $this->subconBlackBeginningOutputEndDate = $subconBlackBeginningOutputEndDate;

        return $this;
    }

    public function getSubconCyanRevisionStartDate(): ?\DateTimeInterface
    {
        return $this->subconCyanRevisionStartDate;
    }

    public function setSubconCyanRevisionStartDate(?\DateTimeInterface $subconCyanRevisionStartDate): self
    {
        $this->subconCyanRevisionStartDate = $subconCyanRevisionStartDate;

        return $this;
    }

    public function getSubconCyanRevisionEndDate(): ?\DateTimeInterface
    {
        return $this->subconCyanRevisionEndDate;
    }

    public function setSubconCyanRevisionEndDate(?\DateTimeInterface $subconCyanRevisionEndDate): self
    {
        $this->subconCyanRevisionEndDate = $subconCyanRevisionEndDate;

        return $this;
    }

    public function getSubconMagentaRevisionStartDate(): ?\DateTimeInterface
    {
        return $this->subconMagentaRevisionStartDate;
    }

    public function setSubconMagentaRevisionStartDate(?\DateTimeInterface $subconMagentaRevisionStartDate): self
    {
        $this->subconMagentaRevisionStartDate = $subconMagentaRevisionStartDate;

        return $this;
    }

    public function getSubconMagentaRevisionEndDate(): ?\DateTimeInterface
    {
        return $this->subconMagentaRevisionEndDate;
    }

    public function setSubconMagentaRevisionEndDate(?\DateTimeInterface $subconMagentaRevisionEndDate): self
    {
        $this->subconMagentaRevisionEndDate = $subconMagentaRevisionEndDate;

        return $this;
    }

    public function getSubconYellowRevisionStartDate(): ?\DateTimeInterface
    {
        return $this->subconYellowRevisionStartDate;
    }

    public function setSubconYellowRevisionStartDate(?\DateTimeInterface $subconYellowRevisionStartDate): self
    {
        $this->subconYellowRevisionStartDate = $subconYellowRevisionStartDate;

        return $this;
    }

    public function getSubconYellowRevisionEndDate(): ?\DateTimeInterface
    {
        return $this->subconYellowRevisionEndDate;
    }

    public function setSubconYellowRevisionEndDate(?\DateTimeInterface $subconYellowRevisionEndDate): self
    {
        $this->subconYellowRevisionEndDate = $subconYellowRevisionEndDate;

        return $this;
    }

    public function getSubconBlackRevisionStartDate(): ?\DateTimeInterface
    {
        return $this->subconBlackRevisionStartDate;
    }

    public function setSubconBlackRevisionStartDate(?\DateTimeInterface $subconBlackRevisionStartDate): self
    {
        $this->subconBlackRevisionStartDate = $subconBlackRevisionStartDate;

        return $this;
    }

    public function getSubconBlackRevisionEndDate(): ?\DateTimeInterface
    {
        return $this->subconBlackRevisionEndDate;
    }

    public function setSubconBlackRevisionEndDate(?\DateTimeInterface $subconBlackRevisionEndDate): self
    {
        $this->subconBlackRevisionEndDate = $subconBlackRevisionEndDate;

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
