<?php

namespace App\Entity\Sale;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\SaleDetail;
use App\Repository\Sale\DeliveryDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: DeliveryDetailRepository::class)]
#[ORM\Table(name: 'sale_delivery_detail')]
class DeliveryDetail extends SaleDetail
{
    public const FSC_NONE = '';
    public const FSC_A = 'FSC_100%';
    public const FSC_B = 'FSC_Mix_Credit';
    public const FSC_C = 'FSC_Mix_%';
    public const FSC_D = 'FSC_Recycle';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $lotNumber = '';

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $packaging = '';

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryDetails')]
    #[Assert\NotNull]
    private ?DeliveryHeader $deliveryHeader = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryDetails')]
    #[Assert\NotNull]
    private ?SaleOrderDetail $saleOrderDetail = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $fscCode = '';

    #[ORM\OneToMany(mappedBy: 'deliveryDetail', targetEntity: SaleReturnDetail::class)]
    private Collection $saleReturnDetails;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $linePO = '';

    #[ORM\OneToMany(mappedBy: 'deliveryDetail', targetEntity: SaleInvoiceDetail::class)]
    private Collection $saleInvoiceDetails;

    #[ORM\ManyToOne(inversedBy: 'deliveryDetails')]
    #[Assert\NotNull]
    private ?MasterOrderProductDetail $masterOrderProductDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $deliveredQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $remainingQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityCurrent = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityStockInventory = '0.00';

    public function __construct()
    {
        $this->saleReturnDetails = new ArrayCollection();
        $this->saleInvoiceDetails = new ArrayCollection();
    }

    #[Assert\Callback]
    public function validateQuantityRemaining(ExecutionContextInterface $context, $payload)
    {
        if ($this->deliveryHeader->getId() === null) {
            if ($this->saleOrderDetail->getMaximumToleranceQuantity() > $this->saleOrderDetail->getRemainingQuantityDelivery()) {
                $context->buildViolation('Quantity must be < max quantity tolerance')->atPath('quantity')->addViolation();
            }
        }
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->deliveryHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncTotalReturn(): string
    {
        $total = 0.00;
        foreach ($this->saleReturnDetails as $saleReturnDetail) {
            if (!$saleReturnDetail->isIsCanceled()) {
                $total += $saleReturnDetail->getTotal();
            }
        }
        return $total;
    }

    public function getMasterOrderOrdinalYear() 
    {
        return sprintf('%04d.%02d', intval($this->getMasterOrderProductDetail()->getMasterOrderHeader()->getCodeNumberOrdinal()), intval($this->getMasterOrderProductDetail()->getMasterOrderHeader()->getCodeNumberYear()));
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLotNumber(): ?string
    {
        return $this->lotNumber;
    }

    public function setLotNumber(string $lotNumber): self
    {
        $this->lotNumber = $lotNumber;

        return $this;
    }

    public function getPackaging(): ?string
    {
        return $this->packaging;
    }

    public function setPackaging(string $packaging): self
    {
        $this->packaging = $packaging;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getDeliveryHeader(): ?DeliveryHeader
    {
        return $this->deliveryHeader;
    }

    public function setDeliveryHeader(?DeliveryHeader $deliveryHeader): self
    {
        $this->deliveryHeader = $deliveryHeader;

        return $this;
    }

    public function getSaleOrderDetail(): ?SaleOrderDetail
    {
        return $this->saleOrderDetail;
    }

    public function setSaleOrderDetail(?SaleOrderDetail $saleOrderDetail): self
    {
        $this->saleOrderDetail = $saleOrderDetail;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getFscCode(): ?string
    {
        return $this->fscCode;
    }

    public function setFscCode(string $fscCode): self
    {
        $this->fscCode = $fscCode;

        return $this;
    }

    /**
     * @return Collection<int, SaleReturnDetail>
     */
    public function getSaleReturnDetails(): Collection
    {
        return $this->saleReturnDetails;
    }

    public function addSaleReturnDetail(SaleReturnDetail $saleReturnDetail): self
    {
        if (!$this->saleReturnDetails->contains($saleReturnDetail)) {
            $this->saleReturnDetails->add($saleReturnDetail);
            $saleReturnDetail->setDeliveryDetail($this);
        }

        return $this;
    }

    public function removeSaleReturnDetail(SaleReturnDetail $saleReturnDetail): self
    {
        if ($this->saleReturnDetails->removeElement($saleReturnDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleReturnDetail->getDeliveryDetail() === $this) {
                $saleReturnDetail->setDeliveryDetail(null);
            }
        }

        return $this;
    }

    public function getLinePO(): ?string
    {
        return $this->linePO;
    }

    public function setLinePO(string $linePO): self
    {
        $this->linePO = $linePO;

        return $this;
    }

    /**
     * @return Collection<int, SaleInvoiceDetail>
     */
    public function getSaleInvoiceDetails(): Collection
    {
        return $this->saleInvoiceDetails;
    }

    public function addSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail): self
    {
        if (!$this->saleInvoiceDetails->contains($saleInvoiceDetail)) {
            $this->saleInvoiceDetails->add($saleInvoiceDetail);
            $saleInvoiceDetail->setDeliveryDetail($this);
        }

        return $this;
    }

    public function removeSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail): self
    {
        if ($this->saleInvoiceDetails->removeElement($saleInvoiceDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleInvoiceDetail->getDeliveryDetail() === $this) {
                $saleInvoiceDetail->setDeliveryDetail(null);
            }
        }

        return $this;
    }

    public function getMasterOrderProductDetail(): ?MasterOrderProductDetail
    {
        return $this->masterOrderProductDetail;
    }

    public function setMasterOrderProductDetail(?MasterOrderProductDetail $masterOrderProductDetail): self
    {
        $this->masterOrderProductDetail = $masterOrderProductDetail;

        return $this;
    }

    public function getDeliveredQuantity(): ?string
    {
        return $this->deliveredQuantity;
    }

    public function setDeliveredQuantity(string $deliveredQuantity): self
    {
        $this->deliveredQuantity = $deliveredQuantity;

        return $this;
    }

    public function getRemainingQuantity(): ?string
    {
        return $this->remainingQuantity;
    }

    public function setRemainingQuantity(string $remainingQuantity): self
    {
        $this->remainingQuantity = $remainingQuantity;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityCurrent(): ?string
    {
        return $this->quantityCurrent;
    }

    public function setQuantityCurrent(string $quantityCurrent): self
    {
        $this->quantityCurrent = $quantityCurrent;

        return $this;
    }

    public function getQuantityStockInventory(): ?string
    {
        return $this->quantityStockInventory;
    }

    public function setQuantityStockInventory(string $quantityStockInventory): self
    {
        $this->quantityStockInventory = $quantityStockInventory;

        return $this;
    }
}
