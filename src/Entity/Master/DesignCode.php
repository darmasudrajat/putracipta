<?php

namespace App\Entity\Master;

use App\Entity\Admin\User;
use App\Entity\Master;
use App\Entity\Production\MasterOrderHeader;
use App\Repository\Master\DesignCodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DesignCodeRepository::class)]
#[ORM\Table(name: 'master_design_code')]
#[UniqueEntity(['code', 'variant', 'version'])]
class DesignCode extends Master
{
    public const STATUS_FA = 'fa';
    public const STATUS_NA = 'na';
    public const HOT_STAMPING_GOLD = 'gold';
    public const HOT_STAMPING_SILVER = 'silver';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $version = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $variant = '';

    #[ORM\ManyToOne(inversedBy: 'designCodes')]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\Column(length: 60)]
    private ?string $color = '';

    #[ORM\Column(length: 60)]
    private ?string $pantone = '';

    #[ORM\OneToMany(mappedBy: 'designCode', targetEntity: MasterOrderHeader::class)]
    private Collection $masterOrderHeaders;

    #[ORM\Column(length: 20)]
    private ?string $coating = '';

    #[ORM\Column(length: 200)]
    private ?string $code = '';

    #[ORM\Column(length: 60)]
    private ?string $colorSpecial1 = '';

    #[ORM\Column(length: 60)]
    private ?string $colorSpecial2 = '';

    #[ORM\Column(length: 60)]
    private ?string $colorSpecial3 = '';

    #[ORM\Column(length: 60)]
    private ?string $colorSpecial4 = '';

    #[ORM\Column]
    private ?int $printingUpQuantity = 0;

    #[ORM\Column(length: 60)]
    private ?string $printingKrisSize = '';

    #[ORM\Column(length: 60)]
    private ?string $paperMountage = '';

    #[ORM\ManyToOne(inversedBy: 'designCodes')]
    private ?DiecutKnife $diecutKnife = null;

    #[ORM\ManyToOne(inversedBy: 'designCodes')]
    private ?DielineMillar $dielineMillar = null;

    #[ORM\Column(length: 60)]
    private ?string $status = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $paperCuttingLength = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $paperCuttingWidth = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkCyanPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkMagentaPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkYellowPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkBlackPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkOpvPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK1Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK2Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK3Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK4Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingGlueQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingRubberQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingPaperQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingBoxQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingTapeLargeQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingTapeSmallQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingPlasticQuantity = '0.00';

    #[ORM\OneToMany(mappedBy: 'designCode', targetEntity: DesignCodeProcessDetail::class)]
    private Collection $designCodeProcessDetails;

    #[ORM\Column(length: 60)]
    private ?string $hotStamping = '';

    #[ORM\OneToMany(mappedBy: 'designCode', targetEntity: DesignCodeCheckSheetDetail::class)]
    private Collection $designCodeCheckSheetDetails;

    #[ORM\OneToMany(mappedBy: 'designCode', targetEntity: DesignCodeDistributionDetail::class)]
    private Collection $designCodeDistributionDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $paperPlanoLength = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $paperPlanoWidth = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $glossiness = '0.00';

    #[ORM\OneToMany(mappedBy: 'designCode', targetEntity: DesignCodeProductDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $designCodeProductDetails;

    #[ORM\ManyToOne(inversedBy: 'designCodes')]
    private ?Paper $paper = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $createdTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $modifiedTransactionDateTime = null;

    #[ORM\ManyToOne]
    protected ?User $createdTransactionUser = null;

    #[ORM\ManyToOne]
    protected ?User $modifiedTransactionUser = null;

    #[ORM\Column(length: 20)]
    private ?string $emboss = '';

    #[ORM\Column(length: 200)]
    private ?string $designCodeProductList = '';

    public function __construct()
    {
        $this->masterOrderHeaders = new ArrayCollection();
        $this->designCodeProcessDetails = new ArrayCollection();
        $this->designCodeCheckSheetDetails = new ArrayCollection();
        $this->designCodeDistributionDetails = new ArrayCollection();
        $this->designCodeProductDetails = new ArrayCollection();
    }

    public function getCodeNumber(): string
    {
        return str_pad($this->customer->getCode(), 3, '0', STR_PAD_LEFT) . '-P' . str_pad($this->code, 3, '0', STR_PAD_LEFT) . '-V' . str_pad($this->variant, 3, '0', STR_PAD_LEFT) . '-R' . $this->version;
    }
    
    public function getColorPantoneAdditional() 
    {
        $colorSpecialList = [];
        if ($this->getColorSpecial1() !== '') {
            $colorSpecialList[] = $this->getColorSpecial1();
        }
        if ($this->getColorSpecial2() !== '') {
            $colorSpecialList[] = $this->getColorSpecial2();
        }
        if ($this->getColorSpecial3() !== '') {
            $colorSpecialList[] = $this->getColorSpecial3();
        }
        if ($this->getColorSpecial4() !== '') {
            $colorSpecialList[] = $this->getColorSpecial4();
        }
        
        return implode(', ', $colorSpecialList);
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
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

    public function getPantone(): ?string
    {
        return $this->pantone;
    }

    public function setPantone(string $pantone): self
    {
        $this->pantone = $pantone;

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderHeader>
     */
    public function getMasterOrderHeaders(): Collection
    {
        return $this->masterOrderHeaders;
    }

    public function addMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if (!$this->masterOrderHeaders->contains($masterOrderHeader)) {
            $this->masterOrderHeaders->add($masterOrderHeader);
            $masterOrderHeader->setDesignCode($this);
        }

        return $this;
    }

    public function removeMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if ($this->masterOrderHeaders->removeElement($masterOrderHeader)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderHeader->getDesignCode() === $this) {
                $masterOrderHeader->setDesignCode(null);
            }
        }

        return $this;
    }

    public function getCoating(): ?string
    {
        return $this->coating;
    }

    public function setCoating(string $coating): self
    {
        $this->coating = $coating;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getColorSpecial1(): ?string
    {
        return $this->colorSpecial1;
    }

    public function setColorSpecial1(string $colorSpecial1): self
    {
        $this->colorSpecial1 = $colorSpecial1;

        return $this;
    }

    public function getColorSpecial2(): ?string
    {
        return $this->colorSpecial2;
    }

    public function setColorSpecial2(string $colorSpecial2): self
    {
        $this->colorSpecial2 = $colorSpecial2;

        return $this;
    }

    public function getColorSpecial3(): ?string
    {
        return $this->colorSpecial3;
    }

    public function setColorSpecial3(string $colorSpecial3): self
    {
        $this->colorSpecial3 = $colorSpecial3;

        return $this;
    }

    public function getColorSpecial4(): ?string
    {
        return $this->colorSpecial4;
    }

    public function setColorSpecial4(string $colorSpecial4): self
    {
        $this->colorSpecial4 = $colorSpecial4;

        return $this;
    }

    public function getPrintingUpQuantity(): ?int
    {
        return $this->printingUpQuantity;
    }

    public function setPrintingUpQuantity(int $printingUpQuantity): self
    {
        $this->printingUpQuantity = $printingUpQuantity;

        return $this;
    }

    public function getPrintingKrisSize(): ?string
    {
        return $this->printingKrisSize;
    }

    public function setPrintingKrisSize(string $printingKrisSize): self
    {
        $this->printingKrisSize = $printingKrisSize;

        return $this;
    }

    public function getPaperMountage(): ?string
    {
        return $this->paperMountage;
    }

    public function setPaperMountage(string $paperMountage): self
    {
        $this->paperMountage = $paperMountage;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPaperCuttingLength(): ?string
    {
        return $this->paperCuttingLength;
    }

    public function setPaperCuttingLength(string $paperCuttingLength): self
    {
        $this->paperCuttingLength = $paperCuttingLength;

        return $this;
    }

    public function getPaperCuttingWidth(): ?string
    {
        return $this->paperCuttingWidth;
    }

    public function setPaperCuttingWidth(string $paperCuttingWidth): self
    {
        $this->paperCuttingWidth = $paperCuttingWidth;

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

    public function getInkMagentaPercentage(): ?string
    {
        return $this->inkMagentaPercentage;
    }

    public function setInkMagentaPercentage(string $inkMagentaPercentage): self
    {
        $this->inkMagentaPercentage = $inkMagentaPercentage;

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

    public function getInkBlackPercentage(): ?string
    {
        return $this->inkBlackPercentage;
    }

    public function setInkBlackPercentage(string $inkBlackPercentage): self
    {
        $this->inkBlackPercentage = $inkBlackPercentage;

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

    public function getInkK1Percentage(): ?string
    {
        return $this->inkK1Percentage;
    }

    public function setInkK1Percentage(string $inkK1Percentage): self
    {
        $this->inkK1Percentage = $inkK1Percentage;

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

    public function getInkK3Percentage(): ?string
    {
        return $this->inkK3Percentage;
    }

    public function setInkK3Percentage(string $inkK3Percentage): self
    {
        $this->inkK3Percentage = $inkK3Percentage;

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

    public function getPackagingGlueQuantity(): ?string
    {
        return $this->packagingGlueQuantity;
    }

    public function setPackagingGlueQuantity(string $packagingGlueQuantity): self
    {
        $this->packagingGlueQuantity = $packagingGlueQuantity;

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

    public function getPackagingPaperQuantity(): ?string
    {
        return $this->packagingPaperQuantity;
    }

    public function setPackagingPaperQuantity(string $packagingPaperQuantity): self
    {
        $this->packagingPaperQuantity = $packagingPaperQuantity;

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

    public function getPackagingTapeLargeQuantity(): ?string
    {
        return $this->packagingTapeLargeQuantity;
    }

    public function setPackagingTapeLargeQuantity(string $packagingTapeLargeQuantity): self
    {
        $this->packagingTapeLargeQuantity = $packagingTapeLargeQuantity;

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

    public function getPackagingPlasticQuantity(): ?string
    {
        return $this->packagingPlasticQuantity;
    }

    public function setPackagingPlasticQuantity(string $packagingPlasticQuantity): self
    {
        $this->packagingPlasticQuantity = $packagingPlasticQuantity;

        return $this;
    }

    /**
     * @return Collection<int, DesignCodeProcessDetail>
     */
    public function getDesignCodeProcessDetails(): Collection
    {
        return $this->designCodeProcessDetails;
    }

    public function addDesignCodeProcessDetail(DesignCodeProcessDetail $designCodeProcessDetail): self
    {
        if (!$this->designCodeProcessDetails->contains($designCodeProcessDetail)) {
            $this->designCodeProcessDetails->add($designCodeProcessDetail);
            $designCodeProcessDetail->setDesignCode($this);
        }

        return $this;
    }

    public function removeDesignCodeProcessDetail(DesignCodeProcessDetail $designCodeProcessDetail): self
    {
        if ($this->designCodeProcessDetails->removeElement($designCodeProcessDetail)) {
            // set the owning side to null (unless already changed)
            if ($designCodeProcessDetail->getDesignCode() === $this) {
                $designCodeProcessDetail->setDesignCode(null);
            }
        }

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

    /**
     * @return Collection<int, DesignCodeCheckSheetDetail>
     */
    public function getDesignCodeCheckSheetDetails(): Collection
    {
        return $this->designCodeCheckSheetDetails;
    }

    public function addDesignCodeCheckSheetDetail(DesignCodeCheckSheetDetail $designCodeCheckSheetDetail): self
    {
        if (!$this->designCodeCheckSheetDetails->contains($designCodeCheckSheetDetail)) {
            $this->designCodeCheckSheetDetails->add($designCodeCheckSheetDetail);
            $designCodeCheckSheetDetail->setDesignCode($this);
        }

        return $this;
    }

    public function removeDesignCodeCheckSheetDetail(DesignCodeCheckSheetDetail $designCodeCheckSheetDetail): self
    {
        if ($this->designCodeCheckSheetDetails->removeElement($designCodeCheckSheetDetail)) {
            // set the owning side to null (unless already changed)
            if ($designCodeCheckSheetDetail->getDesignCode() === $this) {
                $designCodeCheckSheetDetail->setDesignCode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DesignCodeDistributionDetail>
     */
    public function getDesignCodeDistributionDetails(): Collection
    {
        return $this->designCodeDistributionDetails;
    }

    public function addDesignCodeDistributionDetail(DesignCodeDistributionDetail $designCodeDistributionDetail): self
    {
        if (!$this->designCodeDistributionDetails->contains($designCodeDistributionDetail)) {
            $this->designCodeDistributionDetails->add($designCodeDistributionDetail);
            $designCodeDistributionDetail->setDesignCode($this);
        }

        return $this;
    }

    public function removeDesignCodeDistributionDetail(DesignCodeDistributionDetail $designCodeDistributionDetail): self
    {
        if ($this->designCodeDistributionDetails->removeElement($designCodeDistributionDetail)) {
            // set the owning side to null (unless already changed)
            if ($designCodeDistributionDetail->getDesignCode() === $this) {
                $designCodeDistributionDetail->setDesignCode(null);
            }
        }

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
     * @return Collection<int, DesignCodeProductDetail>
     */
    public function getDesignCodeProductDetails(): Collection
    {
        return $this->designCodeProductDetails;
    }

    public function addDesignCodeProductDetail(DesignCodeProductDetail $designCodeProductDetail): self
    {
        if (!$this->designCodeProductDetails->contains($designCodeProductDetail)) {
            $this->designCodeProductDetails->add($designCodeProductDetail);
            $designCodeProductDetail->setDesignCode($this);
        }

        return $this;
    }

    public function removeDesignCodeProductDetail(DesignCodeProductDetail $designCodeProductDetail): self
    {
        if ($this->designCodeProductDetails->removeElement($designCodeProductDetail)) {
            // set the owning side to null (unless already changed)
            if ($designCodeProductDetail->getDesignCode() === $this) {
                $designCodeProductDetail->setDesignCode(null);
            }
        }

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
    
    public function getCreatedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->createdTransactionDateTime;
    }

    public function setCreatedTransactionDateTime(?\DateTimeInterface $createdTransactionDateTime): self
    {
        $this->createdTransactionDateTime = $createdTransactionDateTime;

        return $this;
    }

    public function getModifiedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->modifiedTransactionDateTime;
    }

    public function setModifiedTransactionDateTime(?\DateTimeInterface $modifiedTransactionDateTime): self
    {
        $this->modifiedTransactionDateTime = $modifiedTransactionDateTime;

        return $this;
    }

    public function getCreatedTransactionUser(): ?User
    {
        return $this->createdTransactionUser;
    }

    public function setCreatedTransactionUser(?User $createdTransactionUser): self
    {
        $this->createdTransactionUser = $createdTransactionUser;

        return $this;
    }

    public function getModifiedTransactionUser(): ?User
    {
        return $this->modifiedTransactionUser;
    }

    public function setModifiedTransactionUser(?User $modifiedTransactionUser): self
    {
        $this->modifiedTransactionUser = $modifiedTransactionUser;

        return $this;
    }

    public function getEmboss(): ?string
    {
        return $this->emboss;
    }

    public function setEmboss(string $emboss): self
    {
        $this->emboss = $emboss;

        return $this;
    }

    public function getDesignCodeProductList(): ?string
    {
        return $this->designCodeProductList;
    }

    public function setDesignCodeProductList(string $designCodeProductList): self
    {
        $this->designCodeProductList = $designCodeProductList;

        return $this;
    }
}