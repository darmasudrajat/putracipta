<?php

namespace App\Entity\Production;

use App\Entity\Master\Customer;
use App\Entity\Master\DesignCode;
use App\Entity\Master\Employee;
use App\Entity\Master\Paper;
use App\Entity\ProductionHeader;
use App\Repository\Production\ProductPrototypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductPrototypeRepository::class)]
#[ORM\Table(name: 'production_product_prototype')]
class ProductPrototype extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'RNP';
    public const DATA_SOURCE_HARD_FA = 'hard_fa';
    public const DATA_SOURCE_EMAIL = 'email';
    public const DATA_SOURCE_CD = 'cd';
    public const DATA_SOURCE_PRINT_SAMPLE = 'print_sample';
    public const COATING_OPV_MATT = 'opv_matt';
    public const COATING_OPV_GLOSSY = 'opv_glossy';
    public const COATING_WB_MATT = 'wb_matt';
    public const COATING_WB_GLOSSY_FULL = 'wb_glossy_full';
    public const COATING_WB_GLOSSY_FREE = 'wb_glossy_free';
    public const COATING_WB_CALENDERING = 'wb_glossy_calendering';
    public const COATING_UV_GLOSSY_FULL = 'uv_glossy_full';
    public const COATING_UV_GLOSSY_FREE = 'uv_glossy_free';
    public const COATING_UV_GLOSSY_SPOT = 'uv_glossy_spot';
    public const LAMINATING_MATT = 'matt';
    public const LAMINATING_DOV = 'dov';
    public const PROCESS_PRINTING = 'printing';
    public const PROCESS_COATING = 'coating';
    public const PROCESS_DIECUT = 'diecut';
    public const PROCESS_EMBOSS = 'emboss';
    public const PROCESS_HOTSTAMP = 'hotstamp';
    public const PROCESS_LOCK_BOTTOM = 'lem_lock_bottom';
    public const PROCESS_STRAIGHT_JOINT = 'lem_straight_joint';
    public const PROCESS_JILID = 'jilid_buku';
    public const DEVELOPMENT_TYPE_EP = 'ep';
    public const DEVELOPMENT_TYPE_FEP = 'fep';
    public const DEVELOPMENT_TYPE_PP = 'pp';
    public const DEVELOPMENT_TYPE_PS = 'ps';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Employee $employee = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\Column(length: 60)]
    private ?string $color = '';

    #[ORM\Column(type: Types::ARRAY)]
    private array $dataSource = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $laminatingList = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $processList = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $developmentTypeList = [];

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $coatingList = [];

    #[ORM\ManyToOne]
    private ?DesignCode $designCode = null;

    #[ORM\OneToMany(mappedBy: 'productPrototype', targetEntity: ProductDevelopment::class)]
    private Collection $productDevelopments;

    #[ORM\OneToMany(mappedBy: 'productPrototype', targetEntity: ProductPrototypeDetail::class)]
    private Collection $productPrototypeDetails;

    #[ORM\OneToMany(mappedBy: 'productPrototype', targetEntity: ProductPrototypePilotDetail::class)]
    private Collection $productPrototypePilotDetails;

    #[ORM\Column(length: 100)]
    private ?string $materialName = '';

    #[ORM\Column(length: 200)]
    private ?string $prototypeProductList = '';

    #[ORM\Column]
    private ?bool $isRead = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityBlade = '0.00';

    #[ORM\Column(length: 200)]
    private ?string $prototypeProductCodeList = '';

    #[ORM\Column(length: 20)]
    private ?string $transactionFileExtension = '';

    public function __construct()
    {
        $this->productDevelopments = new ArrayCollection();
        $this->productPrototypeDetails = new ArrayCollection();
        $this->productPrototypePilotDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getFileName(): string
    {
        return sprintf('NPP_%d_%s_%s.%s', $this->id, $this->prototypeProductCodeList, $this->transactionDate->format('Y-m-d'), $this->transactionFileExtension);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

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

    public function getDataSource(): array
    {
        return $this->dataSource;
    }

    public function setDataSource(array $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function getLaminatingList(): array
    {
        return $this->laminatingList;
    }

    public function setLaminatingList(array $laminatingList): self
    {
        $this->laminatingList = $laminatingList;

        return $this;
    }

    public function getProcessList(): array
    {
        return $this->processList;
    }

    public function setProcessList(array $processList): self
    {
        $this->processList = $processList;

        return $this;
    }

    public function getDevelopmentTypeList(): array
    {
        return $this->developmentTypeList;
    }

    public function setDevelopmentTypeList(array $developmentTypeList): self
    {
        $this->developmentTypeList = $developmentTypeList;

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

    public function getCoatingList(): array
    {
        return $this->coatingList;
    }

    public function setCoatingList(array $coatingList): self
    {
        $this->coatingList = $coatingList;

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

    /**
     * @return Collection<int, ProductDevelopment>
     */
    public function getProductDevelopments(): Collection
    {
        return $this->productDevelopments;
    }

    public function addProductDevelopment(ProductDevelopment $productDevelopment): self
    {
        if (!$this->productDevelopments->contains($productDevelopment)) {
            $this->productDevelopments->add($productDevelopment);
            $productDevelopment->setProductPrototype($this);
        }

        return $this;
    }

    public function removeProductDevelopment(ProductDevelopment $productDevelopment): self
    {
        if ($this->productDevelopments->removeElement($productDevelopment)) {
            // set the owning side to null (unless already changed)
            if ($productDevelopment->getProductPrototype() === $this) {
                $productDevelopment->setProductPrototype(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductPrototypeDetail>
     */
    public function getProductPrototypeDetails(): Collection
    {
        return $this->productPrototypeDetails;
    }

    public function addProductPrototypeDetail(ProductPrototypeDetail $productPrototypeDetail): self
    {
        if (!$this->productPrototypeDetails->contains($productPrototypeDetail)) {
            $this->productPrototypeDetails->add($productPrototypeDetail);
            $productPrototypeDetail->setProductPrototype($this);
        }

        return $this;
    }

    public function removeProductPrototypeDetail(ProductPrototypeDetail $productPrototypeDetail): self
    {
        if ($this->productPrototypeDetails->removeElement($productPrototypeDetail)) {
            // set the owning side to null (unless already changed)
            if ($productPrototypeDetail->getProductPrototype() === $this) {
                $productPrototypeDetail->setProductPrototype(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductPrototypePilotDetail>
     */
    public function getProductPrototypePilotDetails(): Collection
    {
        return $this->productPrototypePilotDetails;
    }

    public function addProductPrototypePilotDetail(ProductPrototypePilotDetail $productPrototypePilotDetail): self
    {
        if (!$this->productPrototypePilotDetails->contains($productPrototypePilotDetail)) {
            $this->productPrototypePilotDetails->add($productPrototypePilotDetail);
            $productPrototypePilotDetail->setProductPrototype($this);
        }

        return $this;
    }

    public function removeProductPrototypePilotDetail(ProductPrototypePilotDetail $productPrototypePilotDetail): self
    {
        if ($this->productPrototypePilotDetails->removeElement($productPrototypePilotDetail)) {
            // set the owning side to null (unless already changed)
            if ($productPrototypePilotDetail->getProductPrototype() === $this) {
                $productPrototypePilotDetail->setProductPrototype(null);
            }
        }

        return $this;
    }

    public function getMaterialName(): ?string
    {
        return $this->materialName;
    }

    public function setMaterialName(string $materialName): self
    {
        $this->materialName = $materialName;

        return $this;
    }

    public function getPrototypeProductList(): ?string
    {
        return $this->prototypeProductList;
    }

    public function setPrototypeProductList(string $prototypeProductList): self
    {
        $this->prototypeProductList = $prototypeProductList;

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getQuantityBlade(): ?string
    {
        return $this->quantityBlade;
    }

    public function setQuantityBlade(string $quantityBlade): self
    {
        $this->quantityBlade = $quantityBlade;

        return $this;
    }

    public function getPrototypeProductCodeList(): ?string
    {
        return $this->prototypeProductCodeList;
    }

    public function setPrototypeProductCodeList(string $prototypeProductCodeList): self
    {
        $this->prototypeProductCodeList = $prototypeProductCodeList;

        return $this;
    }

    public function getTransactionFileExtension(): ?string
    {
        return $this->transactionFileExtension;
    }

    public function setTransactionFileExtension(string $transactionFileExtension): self
    {
        $this->transactionFileExtension = $transactionFileExtension;

        return $this;
    }
}
