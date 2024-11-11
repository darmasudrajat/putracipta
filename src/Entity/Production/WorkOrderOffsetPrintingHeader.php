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

// ----------------


#[ORM\Column]
private ?int $col1name= null;


#[ORM\Column]
private ?int $col1PrintingQuantity = null;

#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
private ?string $col1InkQuantity = null;

#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
private ?string $col1PlateQuantity = null;

#[ORM\Column]
private ?int $col2name = null;

#[ORM\Column]
private ?int $col2PrintingQuantity = null;

#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
private ?string $col2InkQuantity = null;

#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
private ?string $col2PlateQuantity = null;

#[ORM\Column]
private ?int $col3name = null;

#[ORM\Column]
private ?int $col3PrintingQuantity = null;

#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
private ?string $col3InkQuantity = null;

#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
private ?string $col3PlateQuantity = null;

#[ORM\Column]
private ?int $col4name = null;

#[ORM\Column]
private ?int $col4PrintingQuantity = null;

#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
private ?string $col4InkQuantity = null;

#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
private ?string $col4PlateQuantity = null;






//----------------------------




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

// /------------------------

public function getCol1name(): ?string
{
    return $this->col1name;
}

public function getCol1PrintingQuantity(): ?int
{
    return $this->col1PrintingQuantity;
}

public function setCol1PrintingQuantity(int $col1PrintingQuantity): self
{
    $this->col1PrintingQuantity = $col1PrintingQuantity;

    return $this;
}

public function getCol1InkQuantity(): ?string
{
    return $this->col1InkQuantity;
}

public function setCol1kInkQuantity(string $col1InkQuantity): self
{
    $this->col1InkQuantity = $col1InkQuantity;

    return $this;
}

public function getCol1PlateQuantity(): ?string
{
    return $this->col1PlateQuantity;
}

public function setCol1PlateQuantity(string $col1PlateQuantity): self
{
    $this->col1PlateQuantity = $col1PlateQuantity;

    return $this;
}

// --------------------------

public function getCol2name(): ?string
{
    return $this->col2name;
}


public function getCol2PrintingQuantity(): ?int
{
    return $this->col2PrintingQuantity;
}

public function setCol2PrintingQuantity(int $col2PrintingQuantity): self
{
    $this->col2PrintingQuantity = $col2PrintingQuantity;

    return $this;
}

public function getCol2InkQuantity(): ?string
{
    return $this->col2InkQuantity;
}

public function setCol2kInkQuantity(string $col2InkQuantity): self
{
    $this->col2InkQuantity = $col2InkQuantity;

    return $this;
}

public function getCol2PlateQuantity(): ?string
{
    return $this->col2PlateQuantity;
}

public function setCol2PlateQuantity(string $col2PlateQuantity): self
{
    $this->col2PlateQuantity = $col2PlateQuantity;

    return $this;
}

// /---------------------------

public function getCol3name(): ?string
{
    return $this->col3name;
}

public function getCol3PrintingQuantity(): ?int
{
    return $this->col3PrintingQuantity;
}

public function setCol3PrintingQuantity(int $col3PrintingQuantity): self
{
    $this->col3PrintingQuantity = $col3PrintingQuantity;

    return $this;
}

public function getCol3InkQuantity(): ?string
{
    return $this->col3InkQuantity;
}

public function setCol3InkQuantity(string $col3InkQuantity): self
{
    $this->col3InkQuantity = $col3InkQuantity;

    return $this;
}

public function getCol3PlateQuantity(): ?string
{
    return $this->col3PlateQuantity;
}

public function setCol3PlateQuantity(string $col3PlateQuantity): self
{
    $this->col3PlateQuantity = $col3PlateQuantity;

    return $this;
}


// /---------------------------

public function getCol4name(): ?string
{
    return $this->col4name;
}

public function getCol4PrintingQuantity(): ?int
{
    return $this->col4PrintingQuantity;
}

public function setCol4PrintingQuantity(int $col4PrintingQuantity): self
{
    $this->col4PrintingQuantity = $col4PrintingQuantity;

    return $this;
}

public function getCol4InkQuantity(): ?string
{
    return $this->col4InkQuantity;
}

public function setCol4InkQuantity(string $col4InkQuantity): self
{
    $this->col4InkQuantity = $col4InkQuantity;

    return $this;
}

public function getCol4PlateQuantity(): ?string
{
    return $this->col4PlateQuantity;
}

public function setCol4PlateQuantity(string $col4PlateQuantity): self
{
    $this->col4PlateQuantity = $col4PlateQuantity;

    return $this;
}





// ---------------------------------

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
