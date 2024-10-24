<?php

namespace App\Entity\Production;

use App\Entity\Master\Customer;
use App\Entity\Master\DesignCode;
use App\Entity\Master\DiecutKnife;
use App\Entity\Master\DielineMillar;
use App\Entity\Master\MachinePrinting;
use App\Entity\Master\Paper;
use App\Entity\Master\Warehouse;
use App\Entity\ProductionHeader;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Entity\Stock\InventoryProductReceiveHeader;
use App\Entity\Stock\InventoryReleaseHeader;
use App\Entity\Stock\InventoryRequestPaperDetail;
use App\Repository\Production\MasterOrderHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MasterOrderHeaderRepository::class)]
#[ORM\Table(name: 'production_master_order_header')]
class MasterOrderHeader extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'PMO';
    public const PRINTING_STATUS_PROOF_PRINT = 'proof_print';
    public const PRINTING_STATUS_NEW_ORDER = 'new_order';
    public const PRINTING_STATUS_REPEAT_ORDER = 'repeat_order';
    public const PRINTING_STATUS_REVISE_DESIGN = 'revise_design';
    public const DIECUT_BLADE_NEW = 'new';
    public const DIECUT_BLADE_OLD = 'old';
    public const DIECUT_BLADE_REVISION = 'revision';
    public const ORDER_TYPE_REGULAR = 'regular';
    public const ORDER_TYPE_PRINTING_SHORTAGE = 'printing_shortage';
    public const ORDER_TYPE_RETURN_SHORTAGE = 'return_shortage';
    public const ORDER_TYPE_EXTENSION = 'extension';
    public const CHECK_SHEET_PRINTING_INSPECTION = 'printing_inspection';
    public const TRANSACTION_MODE_SALE_ORDER = 'sale_order';
    public const TRANSACTION_MODE_PRODUCT_PROTOTYPE = 'product_prototype';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\Column(length: 60)]
    private ?string $color = '';

    #[ORM\Column(length: 60)]
    private ?string $finishing = '';

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\Column(length: 60)]
    private ?string $dieCutBlade = '';

    #[ORM\Column(length: 20)]
    private ?string $dieLineFilmNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $layoutModelFileExtension = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityPaper = '0.00';

    #[ORM\Column]
    private ?int $paperMountage = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $paperPlanoLength = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $paperPlanoWidth = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $cuttingLengthSize1 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $cuttingWidthSize1 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $cuttingLengthSize2 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $cuttingWidthSize2 = '0.00';

    #[ORM\Column(length: 100)]
    private ?string $paperRequirement = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $paperTotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkCyanPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkCyanWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkMagentaPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkMagentaWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkYellowPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkYellowWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkBlackPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkBlackWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkK1Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkK1Weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkK2Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkK2Weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkK3Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkK3Weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkK4Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkK4Weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkOpvPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkOpvWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkLaminatingSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkLaminatingQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingGlueQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingGlueWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingRubberQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingRubberWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingPaperQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingPaperWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingBoxQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingBoxWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingTapeLargeQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingTapeLargeSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingTapeSmallQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingTapeSmallSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingPlasticQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $packagingPlasticSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $dimensionLength = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $dimensionWidth = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $dimensionHeight = '0.00';

    #[ORM\Column(type: Types::ARRAY)]
    private array $workOrderDistribution = [];

    #[ORM\Column(length: 100)]
    private ?string $printingStatusData = '';

    #[ORM\Column(length: 100)]
    private ?string $diecutBladeData = '';

    #[ORM\Column(length: 60)]
    private ?string $pantone = '';

    #[ORM\Column(length: 60)]
    private ?string $inkK1Color = '';

    #[ORM\Column(length: 60)]
    private ?string $inkK2Color = '';

    #[ORM\Column(length: 60)]
    private ?string $inkK3Color = '';

    #[ORM\Column(length: 60)]
    private ?string $inkK4Color = '';

    #[ORM\Column(type: Types::ARRAY)]
    private array $printingStatus = [];

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    #[Assert\Type('numeric')]
    private ?string $insitSortingPercentage = '0.000';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    #[Assert\Type('numeric')]
    private ?string $insitPrintingPercentage = '0.000';

    #[ORM\ManyToOne]
    private ?MachinePrinting $machinePrinting = null;

    #[ORM\Column(length: 60)]
    private ?string $orderType = '';

    #[ORM\Column(length: 60)]
    private ?string $hotStamping = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $weightPerPiece = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkHotStampingSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $inkHotStampingQuantity = '0.00';

    #[ORM\Column(type: Types::ARRAY)]
    private array $checkSheet = [];

    #[ORM\ManyToOne(inversedBy: 'masterOrderHeaders')]
    private ?PurchaseOrderPaperHeader $purchaseOrderPaperHeader = null;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: WorkOrderColorMixing::class)]
    private Collection $workOrderColorMixings;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: WorkOrderCuttingHeader::class)]
    private Collection $workOrderCuttingHeaders;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: WorkOrderOffsetPrintingHeader::class)]
    private Collection $workOrderOffsetPrintingHeaders;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: WorkOrderPrepress::class)]
    private Collection $workOrderPrepresses;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: WorkOrderVarnishHeader::class)]
    private Collection $workOrderVarnishHeaders;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: WorkOrderVarnishSpotHeader::class)]
    private Collection $workOrderVarnishSpotHeaders;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: MasterOrderDistributionDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $masterOrderDistributionDetails;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: MasterOrderProductDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $masterOrderProductDetails;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: MasterOrderProcessDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $masterOrderProcessDetails;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: MasterOrderCheckSheetDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $masterOrderCheckSheetDetails;

    #[ORM\Column(length: 200)]
    private ?string $orderTypeMemo = '';

    #[ORM\ManyToOne(inversedBy: 'masterOrderHeaders')]
    private ?ProductDevelopment $productDevelopment = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderHeaders')]
    #[Assert\NotNull]
    private ?DesignCode $designCode = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderHeaders')]
    private ?DiecutKnife $diecutKnife = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderHeaders')]
    private ?DielineMillar $dielineMillar = null;

    #[ORM\Column(length: 20)]
    private ?string $mountageSize = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $glossiness = '0.00';

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: InventoryProductReceiveHeader::class)]
    private Collection $inventoryProductReceiveHeaders;

    #[ORM\ManyToOne]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: MasterOrderPrototypeDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 0)]
    private Collection $masterOrderPrototypeDetails;

    #[ORM\Column(length: 100)]
    private ?string $transactionMode = self::TRANSACTION_MODE_SALE_ORDER;

    #[ORM\Column(length: 200)]
    private ?string $saleOrderReferenceNumberList = '';

    #[ORM\Column(length: 200)]
    private ?string $masterOrderProductList = '';

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: InventoryRequestPaperDetail::class)]
    private Collection $inventoryRequestPaperDetails;

    #[ORM\OneToMany(mappedBy: 'masterOrderHeader', targetEntity: InventoryReleaseHeader::class)]
    private Collection $inventoryReleaseHeaders;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $masterOrderProductNameList = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityPrinting = '1.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityPrinting2 = '1.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $insitSortingQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $insitPrintingQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityOrder = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityStock = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityShortage = '1.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityProduction = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalRemainingProduction = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityStockPaper = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalRemainingInventoryReceive = '0.00';

    public function __construct()
    {
        $this->workOrderColorMixings = new ArrayCollection();
        $this->workOrderCuttingHeaders = new ArrayCollection();
        $this->workOrderOffsetPrintingHeaders = new ArrayCollection();
        $this->workOrderPrepresses = new ArrayCollection();
        $this->workOrderVarnishHeaders = new ArrayCollection();
        $this->workOrderVarnishSpotHeaders = new ArrayCollection();
        $this->masterOrderDistributionDetails = new ArrayCollection();
        $this->masterOrderProductDetails = new ArrayCollection();
        $this->masterOrderProcessDetails = new ArrayCollection();
        $this->masterOrderCheckSheetDetails = new ArrayCollection();
        $this->inventoryProductReceiveHeaders = new ArrayCollection();
        $this->masterOrderPrototypeDetails = new ArrayCollection();
        $this->inventoryRequestPaperDetails = new ArrayCollection();
        $this->inventoryReleaseHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }
    
    public function getSyncTotalQuantityOrder() 
    {
        $totalQuantity = 0;
        
        foreach ($this->masterOrderProductDetails as $detail) {
            $totalQuantity += $detail->getQuantityOrder();
        }
        return $totalQuantity;
    }
    
    public function getSyncTotalQuantityStock() 
    {
        $totalQuantity = 0;
        
        foreach ($this->masterOrderProductDetails as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantityStock();
            }
        }
        return $totalQuantity;
    }
    
    public function getSyncTotalQuantityShortage() 
    {
        $totalQuantity = 0;
        
        $detailsField = $this->transactionMode === self::TRANSACTION_MODE_SALE_ORDER ? 'masterOrderProductDetails' : 'masterOrderPrototypeDetails';
        
        foreach ($this->$detailsField as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantityShortage();
            }
        }
        return $totalQuantity;
    }
    
    public function getSyncTotalQuantityProduction() 
    {
        $totalQuantity = 0;
        
        foreach ($this->masterOrderProductDetails as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantityProduction();
            }
        }
        return $totalQuantity;
    }
    
    public function getSyncTotalRemainingProduction() 
    {
        $totalQuantity = 0;
        
        foreach ($this->masterOrderProductDetails as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getRemainingProduction();
            }
        }
        
        return $totalQuantity;
    }
    
    public function getSyncTotalRemainingInventoryReceive() 
    {
        $totalQuantity = 0;
        
        foreach ($this->masterOrderProductDetails as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getRemainingInventoryReceive();
            }
        }
        
        return $totalQuantity;
    }
    
    public function getSyncQuantityPaper(): string
    {
        $totalQuantityShortage = $this->totalQuantityShortage == 0 ? 1 : $this->totalQuantityShortage;
        $quantityPrinting = $this->quantityPrinting == 0 ? 1 : $this->quantityPrinting;
        $paperMountage = $this->paperMountage == 0 ? 1 : $this->paperMountage;
        $quantity = (1 + ($this->insitPrintingPercentage/100) + ($this->insitSortingPercentage/100)) * ($totalQuantityShortage / $quantityPrinting) / $paperMountage / 500;
        
        return $quantity;
    }
    
    public function getSyncPaperTotal() 
    {
        $paperMountage = empty($this->paperMountage) ? 1 : $this->paperMountage;
        
        return $this->quantityPaper * $paperMountage * 500;
    }
    
    public function getSyncInsitPrintingQuantity() 
    {
        $quantityPrinting = $this->quantityPrinting == 0 ? 1 : $this->quantityPrinting;
        return ($this->insitPrintingPercentage/100) * $this->totalQuantityShortage / $quantityPrinting;
    }
    
    public function getSyncInsitSortingQuantity() 
    {
        $quantityPrinting = $this->quantityPrinting == 0 ? 1 : $this->quantityPrinting;
        return ($this->insitSortingPercentage/100) * $this->totalQuantityShortage / $quantityPrinting;
    }
    
    public function getSyncPaperRequirement() 
    {
        return $this->paperTotal - $this->insitPrintingQuantity;
    }
    
    public function getSyncInkCyanWeight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkCyanPercentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkCyanPercentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }

    public function getSyncInkMagentaWeight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkMagentaPercentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkMagentaPercentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }

    public function getSyncInkYellowWeight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkYellowPercentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkYellowPercentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }

    public function getSyncInkBlackWeight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkBlackPercentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkBlackPercentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }

    public function getSyncInkOpvWeight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkOpvPercentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkOpvPercentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }

    public function getSyncInkK1Weight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkK1Percentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkK1Percentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }

    public function getSyncInkK2Weight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkK2Percentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkK2Percentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }

    public function getSyncInkK3Weight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkK3Percentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkK3Percentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }

    public function getSyncInkK4Weight() 
    {
        $weight = 0.00;
        
        if ($this->cuttingLengthSize2 == 0 && $this->cuttingWidthSize2 == 0) {
            $weight = ($this->inkK4Percentage/100) * $this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 5100000;
        } else {
            $weight = ($this->inkK4Percentage/100) * (($this->cuttingLengthSize1 * $this->cuttingWidthSize1 * $this->paperTotal / 2) + ($this->cuttingLengthSize2 * $this->cuttingWidthSize2 * $this->paperTotal / 2)) / 5100000;
        }
        
        return $weight;
    }
    
    public function getSyncInkLaminatingQuantity() 
    {
        return $this->inkLaminatingSize / 1000 * $this->paperRequirement / 300;
    }
    
    public function getSyncPackagingGlueWeight() 
    {
        return $this->packagingGlueQuantity * 0.0057 * $this->totalQuantityShortage / 1000;
    }
    
    public function getSyncPackagingRubberWeight() 
    {
        return $this->packagingRubberQuantity == 0 ? 0 : $this->totalQuantityShortage / $this->packagingRubberQuantity;
    }
    
    public function getSyncPackagingPaperWeight()
    {
        return $this->packagingPaperQuantity == 0 ? 0 : $this->totalQuantityShortage / $this->packagingPaperQuantity;
    }
    
    public function getSyncPackagingBoxWeight() 
    {
        return $this->packagingBoxQuantity == 0 ? 0 : $this->totalQuantityShortage / $this->packagingBoxQuantity;
    }
    
    public function getSyncPackagingTapeLargeSize() 
    {
        return $this->packagingTapeLargeQuantity * ($this->packagingPaperWeight + $this->packagingBoxWeight) / 10000;
    }

    public function getSyncPackagingTapeSmallSize() 
    {
        return $this->packagingTapeSmallQuantity * ($this->packagingPaperQuantity + $this->packagingBoxQuantity) / 10000;
    }
    
    public function getSyncPackagingPlasticSize() 
    {
        $packagingPaperQuantity = $this->packagingPaperQuantity == 0 ? 1 : $this->packagingPaperQuantity;
        $packagingBoxQuantity = $this->packagingBoxQuantity == 0 ? 1 : $this->packagingBoxQuantity;
        
        return $this->packagingPlasticQuantity == 0 ? 0 : 1000 / $this->packagingPlasticQuantity * ($packagingPaperQuantity + $packagingBoxQuantity);
    }

    public function getColorPantoneAdditional() 
    {
        $inkSpecialColorList = [];
        if ($this->getInkK1Color() !== '') {
            $inkSpecialColorList[] = $this->getInkK1Color();
        }
        if ($this->getInkK2Color() !== '') {
            $inkSpecialColorList[] = $this->getInkK2Color();
        }
        if ($this->getInkK3Color() !== '') {
            $inkSpecialColorList[] = $this->getInkK3Color();
        }
        if ($this->getInkK4Color() !== '') {
            $inkSpecialColorList[] = $this->getInkK4Color();
        }
        
        return implode(', ', $inkSpecialColorList);
    }
    
    public function getLotNumber() 
    {
        return $this->codeNumberOrdinal . '.' . $this->codeNumberYear;
    }
    
    public function getFileName(): string
    {
        return sprintf('(%d)_%s_%s.%s', $this->id, $this->masterOrderProductList, $this->transactionDate->format('Y-m-d'), $this->layoutModelFileExtension);
    }
    
    public function getQuantityPlano() 
    {
        return $this->paperTotal / $this->paperMountage;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getFinishing(): ?string
    {
        return $this->finishing;
    }

    public function setFinishing(string $finishing): self
    {
        $this->finishing = $finishing;

        return $this;
    }

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

        return $this;
    }

    public function getDieCutBlade(): ?string
    {
        return $this->dieCutBlade;
    }

    public function setDieCutBlade(string $dieCutBlade): self
    {
        $this->dieCutBlade = $dieCutBlade;

        return $this;
    }

    public function getDieLineFilmNumber(): ?string
    {
        return $this->dieLineFilmNumber;
    }

    public function setDieLineFilmNumber(string $dieLineFilmNumber): self
    {
        $this->dieLineFilmNumber = $dieLineFilmNumber;

        return $this;
    }

    public function getLayoutModelFileExtension(): ?string
    {
        return $this->layoutModelFileExtension;
    }

    public function setLayoutModelFileExtension(string $layoutModelFileExtension): self
    {
        $this->layoutModelFileExtension = $layoutModelFileExtension;

        return $this;
    }

    public function getQuantityPaper(): ?string
    {
        return $this->quantityPaper;
    }

    public function setQuantityPaper(string $quantityPaper): self
    {
        $this->quantityPaper = $quantityPaper;

        return $this;
    }

    public function getPaperMountage(): ?int
    {
        return $this->paperMountage;
    }

    public function setPaperMountage(int $paperMountage): self
    {
        $this->paperMountage = $paperMountage;

        return $this;
    }

    public function getPaperPlanoLength(): ?string
    {
        return $this->paperPlanoLength;
    }

    public function setPaperPlanoLength(string $paperPlanoLength): self
    {
        $this->paperPlanoLength = $paperPlanoLength;

        return $this;
    }

    public function getPaperPlanoWidth(): ?string
    {
        return $this->paperPlanoWidth;
    }

    public function setPaperPlanoWidth(string $paperPlanoWidth): self
    {
        $this->paperPlanoWidth = $paperPlanoWidth;

        return $this;
    }

    public function getCuttingLengthSize1(): ?string
    {
        return $this->cuttingLengthSize1;
    }

    public function setCuttingLengthSize1(string $cuttingLengthSize1): self
    {
        $this->cuttingLengthSize1 = $cuttingLengthSize1;

        return $this;
    }

    public function getCuttingWidthSize1(): ?string
    {
        return $this->cuttingWidthSize1;
    }

    public function setCuttingWidthSize1(string $cuttingWidthSize1): self
    {
        $this->cuttingWidthSize1 = $cuttingWidthSize1;

        return $this;
    }

    public function getCuttingLengthSize2(): ?string
    {
        return $this->cuttingLengthSize2;
    }

    public function setCuttingLengthSize2(string $cuttingLengthSize2): self
    {
        $this->cuttingLengthSize2 = $cuttingLengthSize2;

        return $this;
    }

    public function getCuttingWidthSize2(): ?string
    {
        return $this->cuttingWidthSize2;
    }

    public function setCuttingWidthSize2(string $cuttingWidthSize2): self
    {
        $this->cuttingWidthSize2 = $cuttingWidthSize2;

        return $this;
    }

    public function getPaperRequirement(): ?string
    {
        return $this->paperRequirement;
    }

    public function setPaperRequirement(string $paperRequirement): self
    {
        $this->paperRequirement = $paperRequirement;

        return $this;
    }

    public function getPaperTotal(): ?string
    {
        return $this->paperTotal;
    }

    public function setPaperTotal(string $paperTotal): self
    {
        $this->paperTotal = $paperTotal;

        return $this;
    }

    public function getInkCyanPercentage(): ?string
    {
        return $this->inkCyanPercentage;
    }

    public function setInkCyanPercentage(string $inkCyanPercentage): self
    {
        $this->inkCyanPercentage = $inkCyanPercentage;

        return $this;
    }

    public function getInkCyanWeight(): ?string
    {
        return $this->inkCyanWeight;
    }

    public function setInkCyanWeight(string $inkCyanWeight): self
    {
        $this->inkCyanWeight = $inkCyanWeight;

        return $this;
    }

    public function getInkMagentaPercentage(): ?string
    {
        return $this->inkMagentaPercentage;
    }

    public function setInkMagentaPercentage(string $inkMagentaPercentage): self
    {
        $this->inkMagentaPercentage = $inkMagentaPercentage;

        return $this;
    }

    public function getInkMagentaWeight(): ?string
    {
        return $this->inkMagentaWeight;
    }

    public function setInkMagentaWeight(string $inkMagentaWeight): self
    {
        $this->inkMagentaWeight = $inkMagentaWeight;

        return $this;
    }

    public function getInkYellowPercentage(): ?string
    {
        return $this->inkYellowPercentage;
    }

    public function setInkYellowPercentage(string $inkYellowPercentage): self
    {
        $this->inkYellowPercentage = $inkYellowPercentage;

        return $this;
    }

    public function getInkYellowWeight(): ?string
    {
        return $this->inkYellowWeight;
    }

    public function setInkYellowWeight(string $inkYellowWeight): self
    {
        $this->inkYellowWeight = $inkYellowWeight;

        return $this;
    }

    public function getInkBlackPercentage(): ?string
    {
        return $this->inkBlackPercentage;
    }

    public function setInkBlackPercentage(string $inkBlackPercentage): self
    {
        $this->inkBlackPercentage = $inkBlackPercentage;

        return $this;
    }

    public function getInkBlackWeight(): ?string
    {
        return $this->inkBlackWeight;
    }

    public function setInkBlackWeight(string $inkBlackWeight): self
    {
        $this->inkBlackWeight = $inkBlackWeight;

        return $this;
    }

    public function getInkK1Percentage(): ?string
    {
        return $this->inkK1Percentage;
    }

    public function setInkK1Percentage(string $inkK1Percentage): self
    {
        $this->inkK1Percentage = $inkK1Percentage;

        return $this;
    }

    public function getInkK1Weight(): ?string
    {
        return $this->inkK1Weight;
    }

    public function setInkK1Weight(string $inkK1Weight): self
    {
        $this->inkK1Weight = $inkK1Weight;

        return $this;
    }

    public function getInkK2Percentage(): ?string
    {
        return $this->inkK2Percentage;
    }

    public function setInkK2Percentage(string $inkK2Percentage): self
    {
        $this->inkK2Percentage = $inkK2Percentage;

        return $this;
    }

    public function getInkK2Weight(): ?string
    {
        return $this->inkK2Weight;
    }

    public function setInkK2Weight(string $inkK2Weight): self
    {
        $this->inkK2Weight = $inkK2Weight;

        return $this;
    }

    public function getInkK3Percentage(): ?string
    {
        return $this->inkK3Percentage;
    }

    public function setInkK3Percentage(string $inkK3Percentage): self
    {
        $this->inkK3Percentage = $inkK3Percentage;

        return $this;
    }

    public function getInkK3Weight(): ?string
    {
        return $this->inkK3Weight;
    }

    public function setInkK3Weight(string $inkK3Weight): self
    {
        $this->inkK3Weight = $inkK3Weight;

        return $this;
    }

    public function getInkK4Percentage(): ?string
    {
        return $this->inkK4Percentage;
    }

    public function setInkK4Percentage(string $inkK4Percentage): self
    {
        $this->inkK4Percentage = $inkK4Percentage;

        return $this;
    }

    public function getInkK4Weight(): ?string
    {
        return $this->inkK4Weight;
    }

    public function setInkK4Weight(string $inkK4Weight): self
    {
        $this->inkK4Weight = $inkK4Weight;

        return $this;
    }

    public function getInkOpvPercentage(): ?string
    {
        return $this->inkOpvPercentage;
    }

    public function setInkOpvPercentage(string $inkOpvPercentage): self
    {
        $this->inkOpvPercentage = $inkOpvPercentage;

        return $this;
    }

    public function getInkOpvWeight(): ?string
    {
        return $this->inkOpvWeight;
    }

    public function setInkOpvWeight(string $inkOpvWeight): self
    {
        $this->inkOpvWeight = $inkOpvWeight;

        return $this;
    }

    public function getInkLaminatingSize(): ?string
    {
        return $this->inkLaminatingSize;
    }

    public function setInkLaminatingSize(string $inkLaminatingSize): self
    {
        $this->inkLaminatingSize = $inkLaminatingSize;

        return $this;
    }

    public function getInkLaminatingQuantity(): ?string
    {
        return $this->inkLaminatingQuantity;
    }

    public function setInkLaminatingQuantity(string $inkLaminatingQuantity): self
    {
        $this->inkLaminatingQuantity = $inkLaminatingQuantity;

        return $this;
    }

    public function getPackagingGlueQuantity(): ?string
    {
        return $this->packagingGlueQuantity;
    }

    public function setPackagingGlueQuantity(string $packagingGlueQuantity): self
    {
        $this->packagingGlueQuantity = $packagingGlueQuantity;

        return $this;
    }

    public function getPackagingGlueWeight(): ?string
    {
        return $this->packagingGlueWeight;
    }

    public function setPackagingGlueWeight(string $packagingGlueWeight): self
    {
        $this->packagingGlueWeight = $packagingGlueWeight;

        return $this;
    }

    public function getPackagingRubberQuantity(): ?string
    {
        return $this->packagingRubberQuantity;
    }

    public function setPackagingRubberQuantity(string $packagingRubberQuantity): self
    {
        $this->packagingRubberQuantity = $packagingRubberQuantity;

        return $this;
    }

    public function getPackagingRubberWeight(): ?string
    {
        return $this->packagingRubberWeight;
    }

    public function setPackagingRubberWeight(string $packagingRubberWeight): self
    {
        $this->packagingRubberWeight = $packagingRubberWeight;

        return $this;
    }

    public function getPackagingPaperQuantity(): ?string
    {
        return $this->packagingPaperQuantity;
    }

    public function setPackagingPaperQuantity(string $packagingPaperQuantity): self
    {
        $this->packagingPaperQuantity = $packagingPaperQuantity;

        return $this;
    }

    public function getPackagingPaperWeight(): ?string
    {
        return $this->packagingPaperWeight;
    }

    public function setPackagingPaperWeight(string $packagingPaperWeight): self
    {
        $this->packagingPaperWeight = $packagingPaperWeight;

        return $this;
    }

    public function getPackagingBoxQuantity(): ?string
    {
        return $this->packagingBoxQuantity;
    }

    public function setPackagingBoxQuantity(string $packagingBoxQuantity): self
    {
        $this->packagingBoxQuantity = $packagingBoxQuantity;

        return $this;
    }

    public function getPackagingBoxWeight(): ?string
    {
        return $this->packagingBoxWeight;
    }

    public function setPackagingBoxWeight(string $packagingBoxWeight): self
    {
        $this->packagingBoxWeight = $packagingBoxWeight;

        return $this;
    }

    public function getPackagingTapeLargeQuantity(): ?string
    {
        return $this->packagingTapeLargeQuantity;
    }

    public function setPackagingTapeLargeQuantity(string $packagingTapeLargeQuantity): self
    {
        $this->packagingTapeLargeQuantity = $packagingTapeLargeQuantity;

        return $this;
    }

    public function getPackagingTapeLargeSize(): ?string
    {
        return $this->packagingTapeLargeSize;
    }

    public function setPackagingTapeLargeSize(string $packagingTapeLargeSize): self
    {
        $this->packagingTapeLargeSize = $packagingTapeLargeSize;

        return $this;
    }

    public function getPackagingTapeSmallQuantity(): ?string
    {
        return $this->packagingTapeSmallQuantity;
    }

    public function setPackagingTapeSmallQuantity(string $packagingTapeSmallQuantity): self
    {
        $this->packagingTapeSmallQuantity = $packagingTapeSmallQuantity;

        return $this;
    }

    public function getPackagingTapeSmallSize(): ?string
    {
        return $this->packagingTapeSmallSize;
    }

    public function setPackagingTapeSmallSize(string $packagingTapeSmallSize): self
    {
        $this->packagingTapeSmallSize = $packagingTapeSmallSize;

        return $this;
    }

    public function getPackagingPlasticQuantity(): ?string
    {
        return $this->packagingPlasticQuantity;
    }

    public function setPackagingPlasticQuantity(string $packagingPlasticQuantity): self
    {
        $this->packagingPlasticQuantity = $packagingPlasticQuantity;

        return $this;
    }

    public function getPackagingPlasticSize(): ?string
    {
        return $this->packagingPlasticSize;
    }

    public function setPackagingPlasticSize(string $packagingPlasticSize): self
    {
        $this->packagingPlasticSize = $packagingPlasticSize;

        return $this;
    }

    public function getDimensionLength(): ?string
    {
        return $this->dimensionLength;
    }

    public function setDimensionLength(string $dimensionLength): self
    {
        $this->dimensionLength = $dimensionLength;

        return $this;
    }

    public function getDimensionWidth(): ?string
    {
        return $this->dimensionWidth;
    }

    public function setDimensionWidth(string $dimensionWidth): self
    {
        $this->dimensionWidth = $dimensionWidth;

        return $this;
    }

    public function getDimensionHeight(): ?string
    {
        return $this->dimensionHeight;
    }

    public function setDimensionHeight(string $dimensionHeight): self
    {
        $this->dimensionHeight = $dimensionHeight;

        return $this;
    }

    public function getWorkOrderDistribution(): array
    {
        return $this->workOrderDistribution;
    }

    public function setWorkOrderDistribution(array $workOrderDistribution): self
    {
        $this->workOrderDistribution = $workOrderDistribution;

        return $this;
    }

    public function getPrintingStatusData(): ?string
    {
        return $this->printingStatusData;
    }

    public function setPrintingStatusData(string $printingStatusData): self
    {
        $this->printingStatusData = $printingStatusData;

        return $this;
    }

    public function getDiecutBladeData(): ?string
    {
        return $this->diecutBladeData;
    }

    public function setDiecutBladeData(string $diecutBladeData): self
    {
        $this->diecutBladeData = $diecutBladeData;

        return $this;
    }

    public function getPantone(): ?string
    {
        return $this->pantone;
    }

    public function setPantone(string $pantone): self
    {
        $this->pantone = $pantone;

        return $this;
    }

    public function getInkK1Color(): ?string
    {
        return $this->inkK1Color;
    }

    public function setInkK1Color(string $inkK1Color): self
    {
        $this->inkK1Color = $inkK1Color;

        return $this;
    }

    public function getInkK2Color(): ?string
    {
        return $this->inkK2Color;
    }

    public function setInkK2Color(string $inkK2Color): self
    {
        $this->inkK2Color = $inkK2Color;

        return $this;
    }

    public function getInkK3Color(): ?string
    {
        return $this->inkK3Color;
    }

    public function setInkK3Color(string $inkK3Color): self
    {
        $this->inkK3Color = $inkK3Color;

        return $this;
    }

    public function getInkK4Color(): ?string
    {
        return $this->inkK4Color;
    }

    public function setInkK4Color(string $inkK4Color): self
    {
        $this->inkK4Color = $inkK4Color;

        return $this;
    }

    public function getPrintingStatus(): array
    {
        return $this->printingStatus;
    }

    public function setPrintingStatus(array $printingStatus): self
    {
        $this->printingStatus = $printingStatus;

        return $this;
    }

    public function getInsitSortingPercentage(): ?string
    {
        return $this->insitSortingPercentage;
    }

    public function setInsitSortingPercentage(string $insitSortingPercentage): self
    {
        $this->insitSortingPercentage = $insitSortingPercentage;

        return $this;
    }

    public function getInsitPrintingPercentage(): ?string
    {
        return $this->insitPrintingPercentage;
    }

    public function setInsitPrintingPercentage(string $insitPrintingPercentage): self
    {
        $this->insitPrintingPercentage = $insitPrintingPercentage;

        return $this;
    }

    public function getMachinePrinting(): ?MachinePrinting
    {
        return $this->machinePrinting;
    }

    public function setMachinePrinting(?MachinePrinting $machinePrinting): self
    {
        $this->machinePrinting = $machinePrinting;

        return $this;
    }

    public function getOrderType(): ?string
    {
        return $this->orderType;
    }

    public function setOrderType(string $orderType): self
    {
        $this->orderType = $orderType;

        return $this;
    }

    public function getHotStamping(): ?string
    {
        return $this->hotStamping;
    }

    public function setHotStamping(string $hotStamping): self
    {
        $this->hotStamping = $hotStamping;

        return $this;
    }

    public function getWeightPerPiece(): ?string
    {
        return $this->weightPerPiece;
    }

    public function setWeightPerPiece(string $weightPerPiece): self
    {
        $this->weightPerPiece = $weightPerPiece;

        return $this;
    }

    public function getInkHotStampingSize(): ?string
    {
        return $this->inkHotStampingSize;
    }

    public function setInkHotStampingSize(string $inkHotStampingSize): self
    {
        $this->inkHotStampingSize = $inkHotStampingSize;

        return $this;
    }

    public function getInkHotStampingQuantity(): ?string
    {
        return $this->inkHotStampingQuantity;
    }

    public function setInkHotStampingQuantity(string $inkHotStampingQuantity): self
    {
        $this->inkHotStampingQuantity = $inkHotStampingQuantity;

        return $this;
    }

    public function getCheckSheet(): array
    {
        return $this->checkSheet;
    }

    public function setCheckSheet(array $checkSheet): self
    {
        $this->checkSheet = $checkSheet;

        return $this;
    }

    public function getPurchaseOrderPaperHeader(): ?PurchaseOrderPaperHeader
    {
        return $this->purchaseOrderPaperHeader;
    }

    public function setPurchaseOrderPaperHeader(?PurchaseOrderPaperHeader $purchaseOrderPaperHeader): self
    {
        $this->purchaseOrderPaperHeader = $purchaseOrderPaperHeader;

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderColorMixing>
     */
    public function getWorkOrderColorMixings(): Collection
    {
        return $this->workOrderColorMixings;
    }

    public function addWorkOrderColorMixing(WorkOrderColorMixing $workOrderColorMixing): self
    {
        if (!$this->workOrderColorMixings->contains($workOrderColorMixing)) {
            $this->workOrderColorMixings->add($workOrderColorMixing);
            $workOrderColorMixing->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderColorMixing(WorkOrderColorMixing $workOrderColorMixing): self
    {
        if ($this->workOrderColorMixings->removeElement($workOrderColorMixing)) {
            // set the owning side to null (unless already changed)
            if ($workOrderColorMixing->getMasterOrderHeader() === $this) {
                $workOrderColorMixing->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderCuttingHeader>
     */
    public function getWorkOrderCuttingHeaders(): Collection
    {
        return $this->workOrderCuttingHeaders;
    }

    public function addWorkOrderCuttingHeader(WorkOrderCuttingHeader $workOrderCuttingHeader): self
    {
        if (!$this->workOrderCuttingHeaders->contains($workOrderCuttingHeader)) {
            $this->workOrderCuttingHeaders->add($workOrderCuttingHeader);
            $workOrderCuttingHeader->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderCuttingHeader(WorkOrderCuttingHeader $workOrderCuttingHeader): self
    {
        if ($this->workOrderCuttingHeaders->removeElement($workOrderCuttingHeader)) {
            // set the owning side to null (unless already changed)
            if ($workOrderCuttingHeader->getMasterOrderHeader() === $this) {
                $workOrderCuttingHeader->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderOffsetPrintingHeader>
     */
    public function getWorkOrderOffsetPrintingHeaders(): Collection
    {
        return $this->workOrderOffsetPrintingHeaders;
    }

    public function addWorkOrderOffsetPrintingHeader(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader): self
    {
        if (!$this->workOrderOffsetPrintingHeaders->contains($workOrderOffsetPrintingHeader)) {
            $this->workOrderOffsetPrintingHeaders->add($workOrderOffsetPrintingHeader);
            $workOrderOffsetPrintingHeader->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderOffsetPrintingHeader(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader): self
    {
        if ($this->workOrderOffsetPrintingHeaders->removeElement($workOrderOffsetPrintingHeader)) {
            // set the owning side to null (unless already changed)
            if ($workOrderOffsetPrintingHeader->getMasterOrderHeader() === $this) {
                $workOrderOffsetPrintingHeader->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderPrepress>
     */
    public function getWorkOrderPrepresses(): Collection
    {
        return $this->workOrderPrepresses;
    }

    public function addWorkOrderPrepress(WorkOrderPrepress $workOrderPrepress): self
    {
        if (!$this->workOrderPrepresses->contains($workOrderPrepress)) {
            $this->workOrderPrepresses->add($workOrderPrepress);
            $workOrderPrepress->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderPrepress(WorkOrderPrepress $workOrderPrepress): self
    {
        if ($this->workOrderPrepresses->removeElement($workOrderPrepress)) {
            // set the owning side to null (unless already changed)
            if ($workOrderPrepress->getMasterOrderHeader() === $this) {
                $workOrderPrepress->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderVarnishHeader>
     */
    public function getWorkOrderVarnishHeaders(): Collection
    {
        return $this->workOrderVarnishHeaders;
    }

    public function addWorkOrderVarnishHeader(WorkOrderVarnishHeader $workOrderVarnishHeader): self
    {
        if (!$this->workOrderVarnishHeaders->contains($workOrderVarnishHeader)) {
            $this->workOrderVarnishHeaders->add($workOrderVarnishHeader);
            $workOrderVarnishHeader->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderVarnishHeader(WorkOrderVarnishHeader $workOrderVarnishHeader): self
    {
        if ($this->workOrderVarnishHeaders->removeElement($workOrderVarnishHeader)) {
            // set the owning side to null (unless already changed)
            if ($workOrderVarnishHeader->getMasterOrderHeader() === $this) {
                $workOrderVarnishHeader->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderVarnishSpotHeader>
     */
    public function getWorkOrderVarnishSpotHeaders(): Collection
    {
        return $this->workOrderVarnishSpotHeaders;
    }

    public function addWorkOrderVarnishSpotHeader(WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader): self
    {
        if (!$this->workOrderVarnishSpotHeaders->contains($workOrderVarnishSpotHeader)) {
            $this->workOrderVarnishSpotHeaders->add($workOrderVarnishSpotHeader);
            $workOrderVarnishSpotHeader->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeWorkOrderVarnishSpotHeader(WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader): self
    {
        if ($this->workOrderVarnishSpotHeaders->removeElement($workOrderVarnishSpotHeader)) {
            // set the owning side to null (unless already changed)
            if ($workOrderVarnishSpotHeader->getMasterOrderHeader() === $this) {
                $workOrderVarnishSpotHeader->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderDistributionDetail>
     */
    public function getMasterOrderDistributionDetails(): Collection
    {
        return $this->masterOrderDistributionDetails;
    }

    public function addMasterOrderDistributionDetail(MasterOrderDistributionDetail $masterOrderDistributionDetail): self
    {
        if (!$this->masterOrderDistributionDetails->contains($masterOrderDistributionDetail)) {
            $this->masterOrderDistributionDetails->add($masterOrderDistributionDetail);
            $masterOrderDistributionDetail->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeMasterOrderDistributionDetail(MasterOrderDistributionDetail $masterOrderDistributionDetail): self
    {
        if ($this->masterOrderDistributionDetails->removeElement($masterOrderDistributionDetail)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderDistributionDetail->getMasterOrderHeader() === $this) {
                $masterOrderDistributionDetail->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderProductDetail>
     */
    public function getMasterOrderProductDetails(): Collection
    {
        return $this->masterOrderProductDetails;
    }

    public function addMasterOrderProductDetail(MasterOrderProductDetail $masterOrderProductDetail): self
    {
        if (!$this->masterOrderProductDetails->contains($masterOrderProductDetail)) {
            $this->masterOrderProductDetails->add($masterOrderProductDetail);
            $masterOrderProductDetail->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeMasterOrderProductDetail(MasterOrderProductDetail $masterOrderProductDetail): self
    {
        if ($this->masterOrderProductDetails->removeElement($masterOrderProductDetail)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderProductDetail->getMasterOrderHeader() === $this) {
                $masterOrderProductDetail->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderProcessDetail>
     */
    public function getMasterOrderProcessDetails(): Collection
    {
        return $this->masterOrderProcessDetails;
    }

    public function addMasterOrderProcessDetail(MasterOrderProcessDetail $masterOrderProcessDetail): self
    {
        if (!$this->masterOrderProcessDetails->contains($masterOrderProcessDetail)) {
            $this->masterOrderProcessDetails->add($masterOrderProcessDetail);
            $masterOrderProcessDetail->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeMasterOrderProcessDetail(MasterOrderProcessDetail $masterOrderProcessDetail): self
    {
        if ($this->masterOrderProcessDetails->removeElement($masterOrderProcessDetail)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderProcessDetail->getMasterOrderHeader() === $this) {
                $masterOrderProcessDetail->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderCheckSheetDetail>
     */
    public function getMasterOrderCheckSheetDetails(): Collection
    {
        return $this->masterOrderCheckSheetDetails;
    }

    public function addMasterOrderCheckSheetDetail(MasterOrderCheckSheetDetail $masterOrderCheckSheetDetail): self
    {
        if (!$this->masterOrderCheckSheetDetails->contains($masterOrderCheckSheetDetail)) {
            $this->masterOrderCheckSheetDetails->add($masterOrderCheckSheetDetail);
            $masterOrderCheckSheetDetail->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeMasterOrderCheckSheetDetail(MasterOrderCheckSheetDetail $masterOrderCheckSheetDetail): self
    {
        if ($this->masterOrderCheckSheetDetails->removeElement($masterOrderCheckSheetDetail)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderCheckSheetDetail->getMasterOrderHeader() === $this) {
                $masterOrderCheckSheetDetail->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    public function getOrderTypeMemo(): ?string
    {
        return $this->orderTypeMemo;
    }

    public function setOrderTypeMemo(string $orderTypeMemo): self
    {
        $this->orderTypeMemo = $orderTypeMemo;

        return $this;
    }

    public function getProductDevelopment(): ?ProductDevelopment
    {
        return $this->productDevelopment;
    }

    public function setProductDevelopment(?ProductDevelopment $productDevelopment): self
    {
        $this->productDevelopment = $productDevelopment;

        return $this;
    }

    public function getDesignCode(): ?DesignCode
    {
        return $this->designCode;
    }

    public function setDesignCode(?DesignCode $designCode): self
    {
        $this->designCode = $designCode;

        return $this;
    }

    public function getDiecutKnife(): ?DiecutKnife
    {
        return $this->diecutKnife;
    }

    public function setDiecutKnife(?DiecutKnife $diecutKnife): self
    {
        $this->diecutKnife = $diecutKnife;

        return $this;
    }

    public function getDielineMillar(): ?DielineMillar
    {
        return $this->dielineMillar;
    }

    public function setDielineMillar(?DielineMillar $dielineMillar): self
    {
        $this->dielineMillar = $dielineMillar;

        return $this;
    }

    public function getMountageSize(): ?string
    {
        return $this->mountageSize;
    }

    public function setMountageSize(string $mountageSize): self
    {
        $this->mountageSize = $mountageSize;

        return $this;
    }

    public function getGlossiness(): ?string
    {
        return $this->glossiness;
    }

    public function setGlossiness(string $glossiness): self
    {
        $this->glossiness = $glossiness;

        return $this;
    }

    /**
     * @return Collection<int, InventoryProductReceiveHeader>
     */
    public function getInventoryProductReceiveHeaders(): Collection
    {
        return $this->inventoryProductReceiveHeaders;
    }

    public function addInventoryProductReceiveHeader(InventoryProductReceiveHeader $inventoryProductReceiveHeader): self
    {
        if (!$this->inventoryProductReceiveHeaders->contains($inventoryProductReceiveHeader)) {
            $this->inventoryProductReceiveHeaders->add($inventoryProductReceiveHeader);
            $inventoryProductReceiveHeader->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeInventoryProductReceiveHeader(InventoryProductReceiveHeader $inventoryProductReceiveHeader): self
    {
        if ($this->inventoryProductReceiveHeaders->removeElement($inventoryProductReceiveHeader)) {
            // set the owning side to null (unless already changed)
            if ($inventoryProductReceiveHeader->getMasterOrderHeader() === $this) {
                $inventoryProductReceiveHeader->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderPrototypeDetail>
     */
    public function getMasterOrderPrototypeDetails(): Collection
    {
        return $this->masterOrderPrototypeDetails;
    }

    public function addMasterOrderPrototypeDetail(MasterOrderPrototypeDetail $masterOrderPrototypeDetail): self
    {
        if (!$this->masterOrderPrototypeDetails->contains($masterOrderPrototypeDetail)) {
            $this->masterOrderPrototypeDetails->add($masterOrderPrototypeDetail);
            $masterOrderPrototypeDetail->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeMasterOrderPrototypeDetail(MasterOrderPrototypeDetail $masterOrderPrototypeDetail): self
    {
        if ($this->masterOrderPrototypeDetails->removeElement($masterOrderPrototypeDetail)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderPrototypeDetail->getMasterOrderHeader() === $this) {
                $masterOrderPrototypeDetail->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    public function getTransactionMode(): ?string
    {
        return $this->transactionMode;
    }

    public function setTransactionMode(string $transactionMode): self
    {
        $this->transactionMode = $transactionMode;

        return $this;
    }

    public function getSaleOrderReferenceNumberList(): ?string
    {
        return $this->saleOrderReferenceNumberList;
    }

    public function setSaleOrderReferenceNumberList(string $saleOrderReferenceNumberList): self
    {
        $this->saleOrderReferenceNumberList = $saleOrderReferenceNumberList;

        return $this;
    }

    public function getMasterOrderProductList(): ?string
    {
        return $this->masterOrderProductList;
    }

    public function setMasterOrderProductList(string $masterOrderProductList): self
    {
        $this->masterOrderProductList = $masterOrderProductList;

        return $this;
    }

    /**
     * @return Collection<int, InventoryRequestPaperDetail>
     */
    public function getInventoryRequestPaperDetails(): Collection
    {
        return $this->inventoryRequestPaperDetails;
    }

    public function addInventoryRequestPaperDetail(InventoryRequestPaperDetail $inventoryRequestPaperDetail): self
    {
        if (!$this->inventoryRequestPaperDetails->contains($inventoryRequestPaperDetail)) {
            $this->inventoryRequestPaperDetails->add($inventoryRequestPaperDetail);
            $inventoryRequestPaperDetail->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeInventoryRequestPaperDetail(InventoryRequestPaperDetail $inventoryRequestPaperDetail): self
    {
        if ($this->inventoryRequestPaperDetails->removeElement($inventoryRequestPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryRequestPaperDetail->getMasterOrderHeader() === $this) {
                $inventoryRequestPaperDetail->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InventoryReleaseHeader>
     */
    public function getInventoryReleaseHeaders(): Collection
    {
        return $this->inventoryReleaseHeaders;
    }

    public function addInventoryReleaseHeader(InventoryReleaseHeader $inventoryReleaseHeader): self
    {
        if (!$this->inventoryReleaseHeaders->contains($inventoryReleaseHeader)) {
            $this->inventoryReleaseHeaders->add($inventoryReleaseHeader);
            $inventoryReleaseHeader->setMasterOrderHeader($this);
        }

        return $this;
    }

    public function removeInventoryReleaseHeader(InventoryReleaseHeader $inventoryReleaseHeader): self
    {
        if ($this->inventoryReleaseHeaders->removeElement($inventoryReleaseHeader)) {
            // set the owning side to null (unless already changed)
            if ($inventoryReleaseHeader->getMasterOrderHeader() === $this) {
                $inventoryReleaseHeader->setMasterOrderHeader(null);
            }
        }

        return $this;
    }

    public function getMasterOrderProductNameList(): ?string
    {
        return $this->masterOrderProductNameList;
    }

    public function setMasterOrderProductNameList(string $masterOrderProductNameList): self
    {
        $this->masterOrderProductNameList = $masterOrderProductNameList;

        return $this;
    }

    public function getQuantityPrinting(): ?string
    {
        return $this->quantityPrinting;
    }

    public function setQuantityPrinting(string $quantityPrinting): self
    {
        $this->quantityPrinting = $quantityPrinting;

        return $this;
    }

    public function getQuantityPrinting2(): ?string
    {
        return $this->quantityPrinting2;
    }

    public function setQuantityPrinting2(string $quantityPrinting2): self
    {
        $this->quantityPrinting2 = $quantityPrinting2;

        return $this;
    }

    public function getInsitSortingQuantity(): ?string
    {
        return $this->insitSortingQuantity;
    }

    public function setInsitSortingQuantity(string $insitSortingQuantity): self
    {
        $this->insitSortingQuantity = $insitSortingQuantity;

        return $this;
    }

    public function getInsitPrintingQuantity(): ?string
    {
        return $this->insitPrintingQuantity;
    }

    public function setInsitPrintingQuantity(string $insitPrintingQuantity): self
    {
        $this->insitPrintingQuantity = $insitPrintingQuantity;

        return $this;
    }

    public function getTotalQuantityOrder(): ?string
    {
        return $this->totalQuantityOrder;
    }

    public function setTotalQuantityOrder(string $totalQuantityOrder): self
    {
        $this->totalQuantityOrder = $totalQuantityOrder;

        return $this;
    }

    public function getTotalQuantityStock(): ?string
    {
        return $this->totalQuantityStock;
    }

    public function setTotalQuantityStock(string $totalQuantityStock): self
    {
        $this->totalQuantityStock = $totalQuantityStock;

        return $this;
    }

    public function getTotalQuantityShortage(): ?string
    {
        return $this->totalQuantityShortage;
    }

    public function setTotalQuantityShortage(string $totalQuantityShortage): self
    {
        $this->totalQuantityShortage = $totalQuantityShortage;

        return $this;
    }

    public function getTotalQuantityProduction(): ?string
    {
        return $this->totalQuantityProduction;
    }

    public function setTotalQuantityProduction(string $totalQuantityProduction): self
    {
        $this->totalQuantityProduction = $totalQuantityProduction;

        return $this;
    }

    public function getTotalRemainingProduction(): ?string
    {
        return $this->totalRemainingProduction;
    }

    public function setTotalRemainingProduction(string $totalRemainingProduction): self
    {
        $this->totalRemainingProduction = $totalRemainingProduction;

        return $this;
    }

    public function getQuantityStockPaper(): ?string
    {
        return $this->quantityStockPaper;
    }

    public function setQuantityStockPaper(string $quantityStockPaper): self
    {
        $this->quantityStockPaper = $quantityStockPaper;

        return $this;
    }

    public function getTotalRemainingInventoryReceive(): ?string
    {
        return $this->totalRemainingInventoryReceive;
    }

    public function setTotalRemainingInventoryReceive(string $totalRemainingInventoryReceive): self
    {
        $this->totalRemainingInventoryReceive = $totalRemainingInventoryReceive;

        return $this;
    }
}
