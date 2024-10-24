<?php

namespace App\Entity\Sale;

use App\Entity\Master\Customer;
use App\Entity\Master\Employee;
use App\Entity\Master\Transportation;
use App\Entity\Master\Warehouse;
use App\Entity\SaleHeader;
use App\Repository\Sale\DeliveryHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeliveryHeaderRepository::class)]
#[ORM\Table(name: 'sale_delivery_header')]
class DeliveryHeader extends SaleHeader
{
    public const CODE_NUMBER_CONSTANT = 'DLV';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'deliveryHeader', targetEntity: DeliveryDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $deliveryDetails;

    #[ORM\ManyToOne]
    private ?Transportation $transportation = null;

    #[ORM\ManyToOne]
    private ?Employee $employee = null;

    #[ORM\Column]
    private ?bool $isUsingFscPaper = false;

    #[ORM\Column(length: 100)]
    private ?string $saleOrderReferenceNumbers = '';

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $deliveryAddressOrdinal = 0;

    #[ORM\Column]
    private ?bool $hasReturnTransaction = false;

    #[ORM\Column(length: 100)]
    private ?string $customerName = '';

    #[ORM\OneToMany(mappedBy: 'deliveryHeader', targetEntity: SaleReturnHeader::class)]
    private Collection $saleReturnHeaders;

    #[ORM\Column(length: 100)]
    private ?string $vehicleName = '';

    #[ORM\Column(length: 20)]
    private ?string $vehiclePlateNumber = '';

    #[ORM\Column(length: 100)]
    private ?string $vehicleDriverName = '';

    #[ORM\Column]
    private ?bool $isUsingOutsourceDelivery = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantity = '0.00';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $deliveryDetailProductList = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $deliveryDetailProductCodeList = '';

    public function __construct()
    {
        $this->deliveryDetails = new ArrayCollection();
        $this->saleReturnHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->deliveryDetails as $deliveryDetail) {
            if (!$deliveryDetail->isIsCanceled()) {
                $totalQuantity += $deliveryDetail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getCodeNumberMemo(): string
    {
        return sprintf('%04d-%02d', intval($this->codeNumberOrdinal), intval($this->codeNumberYear));
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
     * @return Collection<int, DeliveryDetail>
     */
    public function getDeliveryDetails(): Collection
    {
        return $this->deliveryDetails;
    }

    public function addDeliveryDetail(DeliveryDetail $deliveryDetail): self
    {
        if (!$this->deliveryDetails->contains($deliveryDetail)) {
            $this->deliveryDetails->add($deliveryDetail);
            $deliveryDetail->setDeliveryHeader($this);
        }

        return $this;
    }

    public function removeDeliveryDetail(DeliveryDetail $deliveryDetail): self
    {
        if ($this->deliveryDetails->removeElement($deliveryDetail)) {
            // set the owning side to null (unless already changed)
            if ($deliveryDetail->getDeliveryHeader() === $this) {
                $deliveryDetail->setDeliveryHeader(null);
            }
        }

        return $this;
    }

    public function getTransportation(): ?Transportation
    {
        return $this->transportation;
    }

    public function setTransportation(?Transportation $transportation): self
    {
        $this->transportation = $transportation;

        return $this;
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

    public function isIsUsingFscPaper(): ?bool
    {
        return $this->isUsingFscPaper;
    }

    public function setIsUsingFscPaper(bool $isUsingFscPaper): self
    {
        $this->isUsingFscPaper = $isUsingFscPaper;

        return $this;
    }

    public function getSaleOrderReferenceNumbers(): ?string
    {
        return $this->saleOrderReferenceNumbers;
    }

    public function setSaleOrderReferenceNumbers(string $saleOrderReferenceNumbers): self
    {
        $this->saleOrderReferenceNumbers = $saleOrderReferenceNumbers;

        return $this;
    }

    public function getDeliveryAddressOrdinal(): ?int
    {
        return $this->deliveryAddressOrdinal;
    }

    public function setDeliveryAddressOrdinal(int $deliveryAddressOrdinal): self
    {
        $this->deliveryAddressOrdinal = $deliveryAddressOrdinal;

        return $this;
    }

    public function isHasReturnTransaction(): ?bool
    {
        return $this->hasReturnTransaction;
    }

    public function setHasReturnTransaction(bool $hasReturnTransaction): self
    {
        $this->hasReturnTransaction = $hasReturnTransaction;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * @return Collection<int, SaleReturnHeader>
     */
    public function getSaleReturnHeaders(): Collection
    {
        return $this->saleReturnHeaders;
    }

    public function addSaleReturnHeader(SaleReturnHeader $saleReturnHeader): self
    {
        if (!$this->saleReturnHeaders->contains($saleReturnHeader)) {
            $this->saleReturnHeaders->add($saleReturnHeader);
            $saleReturnHeader->setDeliveryHeader($this);
        }

        return $this;
    }

    public function removeSaleReturnHeader(SaleReturnHeader $saleReturnHeader): self
    {
        if ($this->saleReturnHeaders->removeElement($saleReturnHeader)) {
            // set the owning side to null (unless already changed)
            if ($saleReturnHeader->getDeliveryHeader() === $this) {
                $saleReturnHeader->setDeliveryHeader(null);
            }
        }

        return $this;
    }

    public function getVehicleName(): ?string
    {
        return $this->vehicleName;
    }

    public function setVehicleName(string $vehicleName): self
    {
        $this->vehicleName = $vehicleName;

        return $this;
    }

    public function getVehiclePlateNumber(): ?string
    {
        return $this->vehiclePlateNumber;
    }

    public function setVehiclePlateNumber(string $vehiclePlateNumber): self
    {
        $this->vehiclePlateNumber = $vehiclePlateNumber;

        return $this;
    }

    public function getVehicleDriverName(): ?string
    {
        return $this->vehicleDriverName;
    }

    public function setVehicleDriverName(string $vehicleDriverName): self
    {
        $this->vehicleDriverName = $vehicleDriverName;

        return $this;
    }

    public function isIsUsingOutsourceDelivery(): ?bool
    {
        return $this->isUsingOutsourceDelivery;
    }

    public function setIsUsingOutsourceDelivery(bool $isUsingOutsourceDelivery): self
    {
        $this->isUsingOutsourceDelivery = $isUsingOutsourceDelivery;

        return $this;
    }

    public function getTotalQuantity(): ?string
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(string $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getDeliveryDetailProductList(): ?string
    {
        return $this->deliveryDetailProductList;
    }

    public function setDeliveryDetailProductList(string $deliveryDetailProductList): self
    {
        $this->deliveryDetailProductList = $deliveryDetailProductList;

        return $this;
    }

    public function getDeliveryDetailProductCodeList(): ?string
    {
        return $this->deliveryDetailProductCodeList;
    }

    public function setDeliveryDetailProductCodeList(string $deliveryDetailProductCodeList): self
    {
        $this->deliveryDetailProductCodeList = $deliveryDetailProductCodeList;

        return $this;
    }
}
