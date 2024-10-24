<?php

namespace App\Entity\Production;

use App\Entity\Master\Employee;
use App\Entity\ProductionHeader;
use App\Repository\Production\ProductDevelopmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductDevelopmentRepository::class)]
#[ORM\Table(name: 'production_product_development')]
class ProductDevelopment extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'RNP';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productDevelopments')]
    #[Assert\NotNull]
    private ?ProductPrototype $productPrototype = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $developmentTypeList = [];

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Employee $employeeDesigner = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $epArtworkFileDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $epCustomerReviewDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $epSubconDiecutDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $epDielineDevelopmentDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $epImageDeliveryProductionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $epDiecutDeliveryProductionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $epDielineDeliveryProductionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fepArtworkFileDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fepCustomerReviewDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fepSubconDiecutDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fepDielineDevelopmentDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fepImageDeliveryProductionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fepDiecutDeliveryProductionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fepDielineDeliveryProductionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $psArtworkFileDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $psCustomerReviewDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $psSubconDiecutDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $psDielineDevelopmentDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $psImageDeliveryProductionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $psDiecutDeliveryProductionDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $psDielineDeliveryProductionDate = null;

    #[ORM\Column]
    private ?bool $isEpArtworkFileSubmitted = false;

    #[ORM\Column]
    private ?bool $isEpCustomerReviewSubmitted = false;

    #[ORM\Column]
    private ?bool $isEpSubconDiecutSubmitted = false;

    #[ORM\Column]
    private ?bool $isEpDielineDevelopmentSubmitted = false;

    #[ORM\Column]
    private ?bool $isEpImageDeliveryProductionSubmitted = false;

    #[ORM\Column]
    private ?bool $isEpDiecutDeliveryProductionSubmitted = false;

    #[ORM\Column]
    private ?bool $isEpDielineDeliveryProductionSubmitted = false;

    #[ORM\Column]
    private ?bool $isFepArtworkFileSubmitted = false;

    #[ORM\Column]
    private ?bool $isFepCustomerReviewSubmitted = false;

    #[ORM\Column]
    private ?bool $isFepSubconDiecutSubmitted = false;

    #[ORM\Column]
    private ?bool $isFepDielineDevelopmentSubmitted = false;

    #[ORM\Column]
    private ?bool $isFepImageDeliveryProductionSubmitted = false;

    #[ORM\Column]
    private ?bool $isFepDiecutDeliveryProductionSubmitted = false;

    #[ORM\Column]
    private ?bool $isFepDielineDeliveryProductionSubmitted = false;

    #[ORM\Column]
    private ?bool $isPsArtworkFileSubmitted = false;

    #[ORM\Column]
    private ?bool $isPsCustomerReviewSubmitted = false;

    #[ORM\Column]
    private ?bool $isPsSubconDiecutSubmitted = false;

    #[ORM\Column]
    private ?bool $isPsDielineDevelopmentSubmitted = false;

    #[ORM\Column]
    private ?bool $isPsImageDeliveryProductionSubmitted = false;

    #[ORM\Column]
    private ?bool $isPsDiecutDeliveryProductionSubmitted = false;

    #[ORM\Column]
    private ?bool $isPsDielineDeliveryProductionSubmitted = false;

    #[ORM\OneToMany(mappedBy: 'productDevelopment', targetEntity: MasterOrderHeader::class)]
    private Collection $masterOrderHeaders;

    #[ORM\Column(length: 20)]
    private ?string $transactionFileExtension = '';

    #[ORM\OneToMany(mappedBy: 'product_development', targetEntity: ProductDevelopmentDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $productDevelopmentDetails;

    #[ORM\Column(length: 200)]
    private ?string $developmentProductList = '';

    public function __construct()
    {
        $this->masterOrderHeaders = new ArrayCollection();
        $this->productDevelopmentDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getFileName(): string
    {
        return sprintf('RNP_%d_%s_%s.%s', $this->id, $this->developmentProductList, $this->transactionDate->format('Y-m-d'), $this->transactionFileExtension);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductPrototype(): ?ProductPrototype
    {
        return $this->productPrototype;
    }

    public function setProductPrototype(?ProductPrototype $productPrototype): self
    {
        $this->productPrototype = $productPrototype;

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

    public function getEmployeeDesigner(): ?Employee
    {
        return $this->employeeDesigner;
    }

    public function setEmployeeDesigner(?Employee $employeeDesigner): self
    {
        $this->employeeDesigner = $employeeDesigner;

        return $this;
    }

    public function getEpArtworkFileDate(): ?\DateTimeInterface
    {
        return $this->epArtworkFileDate;
    }

    public function setEpArtworkFileDate(?\DateTimeInterface $epArtworkFileDate): self
    {
        $this->epArtworkFileDate = $epArtworkFileDate;

        return $this;
    }

    public function getEpArtWorkFileTime(): ?\DateTimeInterface
    {
        return $this->epArtWorkFileTime;
    }

    public function setEpArtWorkFileTime(?\DateTimeInterface $epArtWorkFileTime): self
    {
        $this->epArtWorkFileTime = $epArtWorkFileTime;

        return $this;
    }

    public function getEpCustomerReviewDate(): ?\DateTimeInterface
    {
        return $this->epCustomerReviewDate;
    }

    public function setEpCustomerReviewDate(?\DateTimeInterface $epCustomerReviewDate): self
    {
        $this->epCustomerReviewDate = $epCustomerReviewDate;

        return $this;
    }

    public function getEpCustomerReviewTime(): ?\DateTimeInterface
    {
        return $this->epCustomerReviewTime;
    }

    public function setEpCustomerReviewTime(?\DateTimeInterface $epCustomerReviewTime): self
    {
        $this->epCustomerReviewTime = $epCustomerReviewTime;

        return $this;
    }

    public function getEpSubconDiecutDate(): ?\DateTimeInterface
    {
        return $this->epSubconDiecutDate;
    }

    public function setEpSubconDiecutDate(?\DateTimeInterface $epSubconDiecutDate): self
    {
        $this->epSubconDiecutDate = $epSubconDiecutDate;

        return $this;
    }

    public function getEpSubConDiecutTime(): ?\DateTimeInterface
    {
        return $this->epSubConDiecutTime;
    }

    public function setEpSubConDiecutTime(?\DateTimeInterface $epSubConDiecutTime): self
    {
        $this->epSubConDiecutTime = $epSubConDiecutTime;

        return $this;
    }

    public function getEpDielineDevelopmentDate(): ?\DateTimeInterface
    {
        return $this->epDielineDevelopmentDate;
    }

    public function setEpDielineDevelopmentDate(?\DateTimeInterface $epDielineDevelopmentDate): self
    {
        $this->epDielineDevelopmentDate = $epDielineDevelopmentDate;

        return $this;
    }

    public function getEpDielineDevelopmentTime(): ?\DateTimeInterface
    {
        return $this->epDielineDevelopmentTime;
    }

    public function setEpDielineDevelopmentTime(?\DateTimeInterface $epDielineDevelopmentTime): self
    {
        $this->epDielineDevelopmentTime = $epDielineDevelopmentTime;

        return $this;
    }

    public function getEpImageDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->epImageDeliveryProductionDate;
    }

    public function setEpImageDeliveryProductionDate(?\DateTimeInterface $epImageDeliveryProductionDate): self
    {
        $this->epImageDeliveryProductionDate = $epImageDeliveryProductionDate;

        return $this;
    }

    public function getEpImageDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->epImageDeliveryProductionTime;
    }

    public function setEpImageDeliveryProductionTime(?\DateTimeInterface $epImageDeliveryProductionTime): self
    {
        $this->epImageDeliveryProductionTime = $epImageDeliveryProductionTime;

        return $this;
    }

    public function getEpDiecutDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->epDiecutDeliveryProductionDate;
    }

    public function setEpDiecutDeliveryProductionDate(?\DateTimeInterface $epDiecutDeliveryProductionDate): self
    {
        $this->epDiecutDeliveryProductionDate = $epDiecutDeliveryProductionDate;

        return $this;
    }

    public function getEpDiecutDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->epDiecutDeliveryProductionTime;
    }

    public function setEpDiecutDeliveryProductionTime(?\DateTimeInterface $epDiecutDeliveryProductionTime): self
    {
        $this->epDiecutDeliveryProductionTime = $epDiecutDeliveryProductionTime;

        return $this;
    }

    public function getEpDielineDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->epDielineDeliveryProductionDate;
    }

    public function setEpDielineDeliveryProductionDate(?\DateTimeInterface $epDielineDeliveryProductionDate): self
    {
        $this->epDielineDeliveryProductionDate = $epDielineDeliveryProductionDate;

        return $this;
    }

    public function getEpDielineDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->epDielineDeliveryProductionTime;
    }

    public function setEpDielineDeliveryProductionTime(?\DateTimeInterface $epDielineDeliveryProductionTime): self
    {
        $this->epDielineDeliveryProductionTime = $epDielineDeliveryProductionTime;

        return $this;
    }

    public function getFepArtworkFileDate(): ?\DateTimeInterface
    {
        return $this->fepArtworkFileDate;
    }

    public function setFepArtworkFileDate(?\DateTimeInterface $fepArtworkFileDate): self
    {
        $this->fepArtworkFileDate = $fepArtworkFileDate;

        return $this;
    }

    public function getFepArtworkFileTime(): ?\DateTimeInterface
    {
        return $this->fepArtworkFileTime;
    }

    public function setFepArtworkFileTime(?\DateTimeInterface $fepArtworkFileTime): self
    {
        $this->fepArtworkFileTime = $fepArtworkFileTime;

        return $this;
    }

    public function getFepCustomerReviewDate(): ?\DateTimeInterface
    {
        return $this->fepCustomerReviewDate;
    }

    public function setFepCustomerReviewDate(?\DateTimeInterface $fepCustomerReviewDate): self
    {
        $this->fepCustomerReviewDate = $fepCustomerReviewDate;

        return $this;
    }

    public function getFepCustomerReviewTime(): ?\DateTimeInterface
    {
        return $this->fepCustomerReviewTime;
    }

    public function setFepCustomerReviewTime(?\DateTimeInterface $fepCustomerReviewTime): self
    {
        $this->fepCustomerReviewTime = $fepCustomerReviewTime;

        return $this;
    }

    public function getFepSubconDiecutDate(): ?\DateTimeInterface
    {
        return $this->fepSubconDiecutDate;
    }

    public function setFepSubconDiecutDate(?\DateTimeInterface $fepSubconDiecutDate): self
    {
        $this->fepSubconDiecutDate = $fepSubconDiecutDate;

        return $this;
    }

    public function getFepSubconDiecutTime(): ?\DateTimeInterface
    {
        return $this->fepSubconDiecutTime;
    }

    public function setFepSubconDiecutTime(?\DateTimeInterface $fepSubconDiecutTime): self
    {
        $this->fepSubconDiecutTime = $fepSubconDiecutTime;

        return $this;
    }

    public function getFepDielineDevelopmentDate(): ?\DateTimeInterface
    {
        return $this->fepDielineDevelopmentDate;
    }

    public function setFepDielineDevelopmentDate(?\DateTimeInterface $fepDielineDevelopmentDate): self
    {
        $this->fepDielineDevelopmentDate = $fepDielineDevelopmentDate;

        return $this;
    }

    public function getFepDielineDevelopmentTime(): ?\DateTimeInterface
    {
        return $this->fepDielineDevelopmentTime;
    }

    public function setFepDielineDevelopmentTime(?\DateTimeInterface $fepDielineDevelopmentTime): self
    {
        $this->fepDielineDevelopmentTime = $fepDielineDevelopmentTime;

        return $this;
    }

    public function getFepImageDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->fepImageDeliveryProductionDate;
    }

    public function setFepImageDeliveryProductionDate(?\DateTimeInterface $fepImageDeliveryProductionDate): self
    {
        $this->fepImageDeliveryProductionDate = $fepImageDeliveryProductionDate;

        return $this;
    }

    public function getFepImageDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->fepImageDeliveryProductionTime;
    }

    public function setFepImageDeliveryProductionTime(?\DateTimeInterface $fepImageDeliveryProductionTime): self
    {
        $this->fepImageDeliveryProductionTime = $fepImageDeliveryProductionTime;

        return $this;
    }

    public function getFepDiecutDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->fepDiecutDeliveryProductionDate;
    }

    public function setFepDiecutDeliveryProductionDate(?\DateTimeInterface $fepDiecutDeliveryProductionDate): self
    {
        $this->fepDiecutDeliveryProductionDate = $fepDiecutDeliveryProductionDate;

        return $this;
    }

    public function getFepDiecutDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->fepDiecutDeliveryProductionTime;
    }

    public function setFepDiecutDeliveryProductionTime(?\DateTimeInterface $fepDiecutDeliveryProductionTime): self
    {
        $this->fepDiecutDeliveryProductionTime = $fepDiecutDeliveryProductionTime;

        return $this;
    }

    public function getFepDielineDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->fepDielineDeliveryProductionDate;
    }

    public function setFepDielineDeliveryProductionDate(?\DateTimeInterface $fepDielineDeliveryProductionDate): self
    {
        $this->fepDielineDeliveryProductionDate = $fepDielineDeliveryProductionDate;

        return $this;
    }

    public function getFepDielineDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->fepDielineDeliveryProductionTime;
    }

    public function setFepDielineDeliveryProductionTime(?\DateTimeInterface $fepDielineDeliveryProductionTime): self
    {
        $this->fepDielineDeliveryProductionTime = $fepDielineDeliveryProductionTime;

        return $this;
    }

    public function getPsArtworkFileDate(): ?\DateTimeInterface
    {
        return $this->psArtworkFileDate;
    }

    public function setPsArtworkFileDate(?\DateTimeInterface $psArtworkFileDate): self
    {
        $this->psArtworkFileDate = $psArtworkFileDate;

        return $this;
    }

    public function getPsArtworkFileTime(): ?\DateTimeInterface
    {
        return $this->psArtworkFileTime;
    }

    public function setPsArtworkFileTime(?\DateTimeInterface $psArtworkFileTime): self
    {
        $this->psArtworkFileTime = $psArtworkFileTime;

        return $this;
    }

    public function getPsCustomerReviewDate(): ?\DateTimeInterface
    {
        return $this->psCustomerReviewDate;
    }

    public function setPsCustomerReviewDate(?\DateTimeInterface $psCustomerReviewDate): self
    {
        $this->psCustomerReviewDate = $psCustomerReviewDate;

        return $this;
    }

    public function getPsCustomerReviewTime(): ?\DateTimeInterface
    {
        return $this->psCustomerReviewTime;
    }

    public function setPsCustomerReviewTime(?\DateTimeInterface $psCustomerReviewTime): self
    {
        $this->psCustomerReviewTime = $psCustomerReviewTime;

        return $this;
    }

    public function getPsSubconDiecutDate(): ?\DateTimeInterface
    {
        return $this->psSubconDiecutDate;
    }

    public function setPsSubconDiecutDate(?\DateTimeInterface $psSubconDiecutDate): self
    {
        $this->psSubconDiecutDate = $psSubconDiecutDate;

        return $this;
    }

    public function getPsSubconDiecutTime(): ?\DateTimeInterface
    {
        return $this->psSubconDiecutTime;
    }

    public function setPsSubconDiecutTime(?\DateTimeInterface $psSubconDiecutTime): self
    {
        $this->psSubconDiecutTime = $psSubconDiecutTime;

        return $this;
    }

    public function getPsDielineDevelopmentDate(): ?\DateTimeInterface
    {
        return $this->psDielineDevelopmentDate;
    }

    public function setPsDielineDevelopmentDate(?\DateTimeInterface $psDielineDevelopmentDate): self
    {
        $this->psDielineDevelopmentDate = $psDielineDevelopmentDate;

        return $this;
    }

    public function getPsDielineDevelopmentTime(): ?\DateTimeInterface
    {
        return $this->psDielineDevelopmentTime;
    }

    public function setPsDielineDevelopmentTime(?\DateTimeInterface $psDielineDevelopmentTime): self
    {
        $this->psDielineDevelopmentTime = $psDielineDevelopmentTime;

        return $this;
    }

    public function getPsImageDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->psImageDeliveryProductionDate;
    }

    public function setPsImageDeliveryProductionDate(?\DateTimeInterface $psImageDeliveryProductionDate): self
    {
        $this->psImageDeliveryProductionDate = $psImageDeliveryProductionDate;

        return $this;
    }

    public function getPsImageDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->psImageDeliveryProductionTime;
    }

    public function setPsImageDeliveryProductionTime(?\DateTimeInterface $psImageDeliveryProductionTime): self
    {
        $this->psImageDeliveryProductionTime = $psImageDeliveryProductionTime;

        return $this;
    }

    public function getPsDiecutDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->psDiecutDeliveryProductionDate;
    }

    public function setPsDiecutDeliveryProductionDate(?\DateTimeInterface $psDiecutDeliveryProductionDate): self
    {
        $this->psDiecutDeliveryProductionDate = $psDiecutDeliveryProductionDate;

        return $this;
    }

    public function getPsDiecutDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->psDiecutDeliveryProductionTime;
    }

    public function setPsDiecutDeliveryProductionTime(?\DateTimeInterface $psDiecutDeliveryProductionTime): self
    {
        $this->psDiecutDeliveryProductionTime = $psDiecutDeliveryProductionTime;

        return $this;
    }

    public function getPsDielineDeliveryProductionDate(): ?\DateTimeInterface
    {
        return $this->psDielineDeliveryProductionDate;
    }

    public function setPsDielineDeliveryProductionDate(?\DateTimeInterface $psDielineDeliveryProductionDate): self
    {
        $this->psDielineDeliveryProductionDate = $psDielineDeliveryProductionDate;

        return $this;
    }

    public function getPsDielineDeliveryProductionTime(): ?\DateTimeInterface
    {
        return $this->psDielineDeliveryProductionTime;
    }

    public function setPsDielineDeliveryProductionTime(?\DateTimeInterface $psDielineDeliveryProductionTime): self
    {
        $this->psDielineDeliveryProductionTime = $psDielineDeliveryProductionTime;

        return $this;
    }

    public function isIsEpArtworkFileSubmitted(): ?bool
    {
        return $this->isEpArtworkFileSubmitted;
    }

    public function setIsEpArtworkFileSubmitted(bool $isEpArtworkFileSubmitted): self
    {
        $this->isEpArtworkFileSubmitted = $isEpArtworkFileSubmitted;

        return $this;
    }

    public function isIsEpCustomerReviewSubmitted(): ?bool
    {
        return $this->isEpCustomerReviewSubmitted;
    }

    public function setIsEpCustomerReviewSubmitted(bool $isEpCustomerReviewSubmitted): self
    {
        $this->isEpCustomerReviewSubmitted = $isEpCustomerReviewSubmitted;

        return $this;
    }

    public function isIsEpSubconDiecutSubmitted(): ?bool
    {
        return $this->isEpSubconDiecutSubmitted;
    }

    public function setIsEpSubconDiecutSubmitted(bool $isEpSubconDiecutSubmitted): self
    {
        $this->isEpSubconDiecutSubmitted = $isEpSubconDiecutSubmitted;

        return $this;
    }

    public function isIsEpDielineDevelopmentSubmitted(): ?bool
    {
        return $this->isEpDielineDevelopmentSubmitted;
    }

    public function setIsEpDielineDevelopmentSubmitted(bool $isEpDielineDevelopmentSubmitted): self
    {
        $this->isEpDielineDevelopmentSubmitted = $isEpDielineDevelopmentSubmitted;

        return $this;
    }

    public function isIsEpImageDeliveryProductionSubmitted(): ?bool
    {
        return $this->isEpImageDeliveryProductionSubmitted;
    }

    public function setIsEpImageDeliveryProductionSubmitted(bool $isEpImageDeliveryProductionSubmitted): self
    {
        $this->isEpImageDeliveryProductionSubmitted = $isEpImageDeliveryProductionSubmitted;

        return $this;
    }

    public function isIsEpDiecutDeliveryProductionSubmitted(): ?bool
    {
        return $this->isEpDiecutDeliveryProductionSubmitted;
    }

    public function setIsEpDiecutDeliveryProductionSubmitted(bool $isEpDiecutDeliveryProductionSubmitted): self
    {
        $this->isEpDiecutDeliveryProductionSubmitted = $isEpDiecutDeliveryProductionSubmitted;

        return $this;
    }

    public function isIsEpDielineDeliveryProductionSubmitted(): ?bool
    {
        return $this->isEpDielineDeliveryProductionSubmitted;
    }

    public function setIsEpDielineDeliveryProductionSubmitted(bool $isEpDielineDeliveryProductionSubmitted): self
    {
        $this->isEpDielineDeliveryProductionSubmitted = $isEpDielineDeliveryProductionSubmitted;

        return $this;
    }

    public function isIsFepArtworkFileSubmitted(): ?bool
    {
        return $this->isFepArtworkFileSubmitted;
    }

    public function setIsFepArtworkFileSubmitted(bool $isFepArtworkFileSubmitted): self
    {
        $this->isFepArtworkFileSubmitted = $isFepArtworkFileSubmitted;

        return $this;
    }

    public function isIsFepCustomerReviewSubmitted(): ?bool
    {
        return $this->isFepCustomerReviewSubmitted;
    }

    public function setIsFepCustomerReviewSubmitted(bool $isFepCustomerReviewSubmitted): self
    {
        $this->isFepCustomerReviewSubmitted = $isFepCustomerReviewSubmitted;

        return $this;
    }

    public function isIsFepSubconDiecutSubmitted(): ?bool
    {
        return $this->isFepSubconDiecutSubmitted;
    }

    public function setIsFepSubconDiecutSubmitted(bool $isFepSubconDiecutSubmitted): self
    {
        $this->isFepSubconDiecutSubmitted = $isFepSubconDiecutSubmitted;

        return $this;
    }

    public function isIsFepDielineDevelopmentSubmitted(): ?bool
    {
        return $this->isFepDielineDevelopmentSubmitted;
    }

    public function setIsFepDielineDevelopmentSubmitted(bool $isFepDielineDevelopmentSubmitted): self
    {
        $this->isFepDielineDevelopmentSubmitted = $isFepDielineDevelopmentSubmitted;

        return $this;
    }

    public function isIsFepImageDeliveryProductionSubmitted(): ?bool
    {
        return $this->isFepImageDeliveryProductionSubmitted;
    }

    public function setIsFepImageDeliveryProductionSubmitted(bool $isFepImageDeliveryProductionSubmitted): self
    {
        $this->isFepImageDeliveryProductionSubmitted = $isFepImageDeliveryProductionSubmitted;

        return $this;
    }

    public function isIsFepDiecutDeliveryProductionSubmitted(): ?bool
    {
        return $this->isFepDiecutDeliveryProductionSubmitted;
    }

    public function setIsFepDiecutDeliveryProductionSubmitted(bool $isFepDiecutDeliveryProductionSubmitted): self
    {
        $this->isFepDiecutDeliveryProductionSubmitted = $isFepDiecutDeliveryProductionSubmitted;

        return $this;
    }

    public function isIsFepDielineDeliveryProductionSubmitted(): ?bool
    {
        return $this->isFepDielineDeliveryProductionSubmitted;
    }

    public function setIsFepDielineDeliveryProductionSubmitted(bool $isFepDielineDeliveryProductionSubmitted): self
    {
        $this->isFepDielineDeliveryProductionSubmitted = $isFepDielineDeliveryProductionSubmitted;

        return $this;
    }

    public function isIsPsArtworkFileSubmitted(): ?bool
    {
        return $this->isPsArtworkFileSubmitted;
    }

    public function setIsPsArtworkFileSubmitted(bool $isPsArtworkFileSubmitted): self
    {
        $this->isPsArtworkFileSubmitted = $isPsArtworkFileSubmitted;

        return $this;
    }

    public function isIsPsCustomerReviewSubmitted(): ?bool
    {
        return $this->isPsCustomerReviewSubmitted;
    }

    public function setIsPsCustomerReviewSubmitted(bool $isPsCustomerReviewSubmitted): self
    {
        $this->isPsCustomerReviewSubmitted = $isPsCustomerReviewSubmitted;

        return $this;
    }

    public function isIsPsSubconDiecutSubmitted(): ?bool
    {
        return $this->isPsSubconDiecutSubmitted;
    }

    public function setIsPsSubconDiecutSubmitted(bool $isPsSubconDiecutSubmitted): self
    {
        $this->isPsSubconDiecutSubmitted = $isPsSubconDiecutSubmitted;

        return $this;
    }

    public function isIsPsDielineDevelopmentSubmitted(): ?bool
    {
        return $this->isPsDielineDevelopmentSubmitted;
    }

    public function setIsPsDielineDevelopmentSubmitted(bool $isPsDielineDevelopmentSubmitted): self
    {
        $this->isPsDielineDevelopmentSubmitted = $isPsDielineDevelopmentSubmitted;

        return $this;
    }

    public function isIsPsImageDeliveryProductionSubmitted(): ?bool
    {
        return $this->isPsImageDeliveryProductionSubmitted;
    }

    public function setIsPsImageDeliveryProductionSubmitted(bool $isPsImageDeliveryProductionSubmitted): self
    {
        $this->isPsImageDeliveryProductionSubmitted = $isPsImageDeliveryProductionSubmitted;

        return $this;
    }

    public function isIsPsDiecutDeliveryProductionSubmitted(): ?bool
    {
        return $this->isPsDiecutDeliveryProductionSubmitted;
    }

    public function setIsPsDiecutDeliveryProductionSubmitted(bool $isPsDiecutDeliveryProductionSubmitted): self
    {
        $this->isPsDiecutDeliveryProductionSubmitted = $isPsDiecutDeliveryProductionSubmitted;

        return $this;
    }

    public function isIsPsDielineDeliveryProductionSubmitted(): ?bool
    {
        return $this->isPsDielineDeliveryProductionSubmitted;
    }

    public function setIsPsDielineDeliveryProductionSubmitted(bool $isPsDielineDeliveryProductionSubmitted): self
    {
        $this->isPsDielineDeliveryProductionSubmitted = $isPsDielineDeliveryProductionSubmitted;

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
            $masterOrderHeader->setProductDevelopment($this);
        }

        return $this;
    }

    public function removeMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if ($this->masterOrderHeaders->removeElement($masterOrderHeader)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderHeader->getProductDevelopment() === $this) {
                $masterOrderHeader->setProductDevelopment(null);
            }
        }

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

    /**
     * @return Collection<int, ProductDevelopmentDetail>
     */
    public function getProductDevelopmentDetails(): Collection
    {
        return $this->productDevelopmentDetails;
    }

    public function addProductDevelopmentDetail(ProductDevelopmentDetail $productDevelopmentDetail): self
    {
        if (!$this->productDevelopmentDetails->contains($productDevelopmentDetail)) {
            $this->productDevelopmentDetails->add($productDevelopmentDetail);
            $productDevelopmentDetail->setProductDevelopment($this);
        }

        return $this;
    }

    public function removeProductDevelopmentDetail(ProductDevelopmentDetail $productDevelopmentDetail): self
    {
        if ($this->productDevelopmentDetails->removeElement($productDevelopmentDetail)) {
            // set the owning side to null (unless already changed)
            if ($productDevelopmentDetail->getProductDevelopment() === $this) {
                $productDevelopmentDetail->setProductDevelopment(null);
            }
        }

        return $this;
    }

    public function getDevelopmentProductList(): ?string
    {
        return $this->developmentProductList;
    }

    public function setDevelopmentProductList(string $developmentProductList): self
    {
        $this->developmentProductList = $developmentProductList;

        return $this;
    }
}
