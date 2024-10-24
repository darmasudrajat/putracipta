<?php

namespace App\Entity\Production;

use App\Entity\ProductionHeader;
use App\Repository\Production\WorkOrderOffsetPrintingHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderOffsetPrintingHeaderRepository::class)]
#[ORM\Table(name: 'production_work_order_offset_printing_header')]
class WorkOrderOffsetPrintingHeader extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'WOP';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $printingQuality = null;

    #[ORM\Column(length: 20)]
    private ?string $speedPerHour = null;

    #[ORM\Column(length: 20)]
    private ?string $waterLevel = null;

    #[ORM\Column(length: 20)]
    private ?string $alcoholLevel = null;

    #[ORM\Column(length: 20)]
    private ?string $fountainLevel = null;

    #[ORM\Column(length: 20)]
    private ?string $wbLevel = null;

    #[ORM\Column]
    private ?int $cyanPrintingQuantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cyanInkQuantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cyanPlateQuantity = null;

    #[ORM\Column]
    private ?int $magentaPrintingQuantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $magentaInkQuantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $magentaPlateQuantity = null;

    #[ORM\Column]
    private ?int $yellowPrintingQuantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $yellowInkQuantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $yellowPlateQuantity = null;

    #[ORM\Column]
    private ?int $blackPrintingQuantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $blackInkQuantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $blackPlateQuantity = null;

    #[ORM\OneToMany(mappedBy: 'workOrderOffsetPrintingHeader', targetEntity: WorkOrderOffsetPrintingDetail::class)]
    private Collection $workOrderOffsetPrintingDetails;

    #[ORM\ManyToOne(inversedBy: 'workOrderOffsetPrintingHeaders')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    public function __construct()
    {
        $this->workOrderOffsetPrintingDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrintingQuality(): ?string
    {
        return $this->printingQuality;
    }

    public function setPrintingQuality(string $printingQuality): self
    {
        $this->printingQuality = $printingQuality;

        return $this;
    }

    public function getSpeedPerHour(): ?string
    {
        return $this->speedPerHour;
    }

    public function setSpeedPerHour(string $speedPerHour): self
    {
        $this->speedPerHour = $speedPerHour;

        return $this;
    }

    public function getWaterLevel(): ?string
    {
        return $this->waterLevel;
    }

    public function setWaterLevel(string $waterLevel): self
    {
        $this->waterLevel = $waterLevel;

        return $this;
    }

    public function getAlcoholLevel(): ?string
    {
        return $this->alcoholLevel;
    }

    public function setAlcoholLevel(string $alcoholLevel): self
    {
        $this->alcoholLevel = $alcoholLevel;

        return $this;
    }

    public function getFountainLevel(): ?string
    {
        return $this->fountainLevel;
    }

    public function setFountainLevel(string $fountainLevel): self
    {
        $this->fountainLevel = $fountainLevel;

        return $this;
    }

    public function getWbLevel(): ?string
    {
        return $this->wbLevel;
    }

    public function setWbLevel(string $wbLevel): self
    {
        $this->wbLevel = $wbLevel;

        return $this;
    }

    public function getCyanPrintingQuantity(): ?int
    {
        return $this->cyanPrintingQuantity;
    }

    public function setCyanPrintingQuantity(int $cyanPrintingQuantity): self
    {
        $this->cyanPrintingQuantity = $cyanPrintingQuantity;

        return $this;
    }

    public function getCyanInkQuantity(): ?string
    {
        return $this->cyanInkQuantity;
    }

    public function setCyanInkQuantity(string $cyanInkQuantity): self
    {
        $this->cyanInkQuantity = $cyanInkQuantity;

        return $this;
    }

    public function getCyanPlateQuantity(): ?string
    {
        return $this->cyanPlateQuantity;
    }

    public function setCyanPlateQuantity(string $cyanPlateQuantity): self
    {
        $this->cyanPlateQuantity = $cyanPlateQuantity;

        return $this;
    }

    public function getMagentaPrintingQuantity(): ?int
    {
        return $this->magentaPrintingQuantity;
    }

    public function setMagentaPrintingQuantity(int $magentaPrintingQuantity): self
    {
        $this->magentaPrintingQuantity = $magentaPrintingQuantity;

        return $this;
    }

    public function getMagentaInkQuantity(): ?string
    {
        return $this->magentaInkQuantity;
    }

    public function setMagentaInkQuantity(string $magentaInkQuantity): self
    {
        $this->magentaInkQuantity = $magentaInkQuantity;

        return $this;
    }

    public function getMagentaPlateQuantity(): ?string
    {
        return $this->magentaPlateQuantity;
    }

    public function setMagentaPlateQuantity(string $magentaPlateQuantity): self
    {
        $this->magentaPlateQuantity = $magentaPlateQuantity;

        return $this;
    }

    public function getYellowPrintingQuantity(): ?int
    {
        return $this->yellowPrintingQuantity;
    }

    public function setYellowPrintingQuantity(int $yellowPrintingQuantity): self
    {
        $this->yellowPrintingQuantity = $yellowPrintingQuantity;

        return $this;
    }

    public function getYellowInkQuantity(): ?string
    {
        return $this->yellowInkQuantity;
    }

    public function setYellowInkQuantity(string $yellowInkQuantity): self
    {
        $this->yellowInkQuantity = $yellowInkQuantity;

        return $this;
    }

    public function getYellowPlateQuantity(): ?string
    {
        return $this->yellowPlateQuantity;
    }

    public function setYellowPlateQuantity(string $yellowPlateQuantity): self
    {
        $this->yellowPlateQuantity = $yellowPlateQuantity;

        return $this;
    }

    public function getBlackPrintingQuantity(): ?int
    {
        return $this->blackPrintingQuantity;
    }

    public function setBlackPrintingQuantity(int $blackPrintingQuantity): self
    {
        $this->blackPrintingQuantity = $blackPrintingQuantity;

        return $this;
    }

    public function getBlackInkQuantity(): ?string
    {
        return $this->blackInkQuantity;
    }

    public function setBlackInkQuantity(string $blackInkQuantity): self
    {
        $this->blackInkQuantity = $blackInkQuantity;

        return $this;
    }

    public function getBlackPlateQuantity(): ?string
    {
        return $this->blackPlateQuantity;
    }

    public function setBlackPlateQuantity(string $blackPlateQuantity): self
    {
        $this->blackPlateQuantity = $blackPlateQuantity;

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderOffsetPrintingDetail>
     */
    public function getWorkOrderOffsetPrintingDetails(): Collection
    {
        return $this->workOrderOffsetPrintingDetails;
    }

    public function addWorkOrderOffsetPrintingDetail(WorkOrderOffsetPrintingDetail $workOrderOffsetPrintingDetail): self
    {
        if (!$this->workOrderOffsetPrintingDetails->contains($workOrderOffsetPrintingDetail)) {
            $this->workOrderOffsetPrintingDetails->add($workOrderOffsetPrintingDetail);
            $workOrderOffsetPrintingDetail->setWorkOrderOffsetPrintingHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderOffsetPrintingDetail(WorkOrderOffsetPrintingDetail $workOrderOffsetPrintingDetail): self
    {
        if ($this->workOrderOffsetPrintingDetails->removeElement($workOrderOffsetPrintingDetail)) {
            // set the owning side to null (unless already changed)
            if ($workOrderOffsetPrintingDetail->getWorkOrderOffsetPrintingHeader() === $this) {
                $workOrderOffsetPrintingDetail->setWorkOrderOffsetPrintingHeader(null);
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
