<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Supplier;
use App\Entity\Master\Warehouse;
use App\Entity\PurchaseHeader;
use App\Repository\Purchase\ReceiveHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReceiveHeaderRepository::class)]
#[ORM\Table(name: 'purchase_receive_header')]
class ReceiveHeader extends PurchaseHeader
{
    public const CODE_NUMBER_CONSTANT = 'RCV';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $supplierDeliveryCodeNumber = '';

    #[ORM\ManyToOne]
//    #[Assert\NotNull]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne(inversedBy: 'receiveHeaders')]
    private ?PurchaseOrderHeader $purchaseOrderHeader = null;

    #[ORM\OneToMany(mappedBy: 'receiveHeader', targetEntity: ReceiveDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $receiveDetails;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\ManyToOne(inversedBy: 'receiveHeaders')]
    private ?PurchaseOrderPaperHeader $purchaseOrderPaperHeader = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $purchaseOrderCodeNumberOrdinal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    private ?int $purchaseOrderCodeNumberMonth = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    private ?int $purchaseOrderCodeNumberYear = 0;

    #[ORM\Column]
    private ?bool $hasReturnTransaction = false;

    #[ORM\OneToMany(mappedBy: 'receiveHeader', targetEntity: PurchaseInvoiceHeader::class)]
    private Collection $purchaseInvoiceHeaders;

    #[ORM\OneToMany(mappedBy: 'receiveHeader', targetEntity: PurchaseReturnHeader::class)]
    private Collection $purchaseReturnHeaders;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantity = '0.00';

    public function __construct()
    {
        $this->receiveDetails = new ArrayCollection();
        $this->purchaseInvoiceHeaders = new ArrayCollection();
        $this->purchaseReturnHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): string
    {
        $totalQuantity = '0.00';
        foreach ($this->receiveDetails as $receiveDetail) {
            if (!$receiveDetail->isIsCanceled()) {
                $totalQuantity += $receiveDetail->getReceivedQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupplierDeliveryCodeNumber(): ?string
    {
        return $this->supplierDeliveryCodeNumber;
    }

    public function setSupplierDeliveryCodeNumber(string $supplierDeliveryCodeNumber): self
    {
        $this->supplierDeliveryCodeNumber = $supplierDeliveryCodeNumber;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getPurchaseOrderHeader(): ?PurchaseOrderHeader
    {
        return $this->purchaseOrderHeader;
    }

    public function setPurchaseOrderHeader(?PurchaseOrderHeader $purchaseOrderHeader): self
    {
        $this->purchaseOrderHeader = $purchaseOrderHeader;

        return $this;
    }

    /**
     * @return Collection<int, ReceiveDetail>
     */
    public function getReceiveDetails(): Collection
    {
        return $this->receiveDetails;
    }

    public function addReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if (!$this->receiveDetails->contains($receiveDetail)) {
            $this->receiveDetails->add($receiveDetail);
            $receiveDetail->setReceiveHeader($this);
        }

        return $this;
    }

    public function removeReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if ($this->receiveDetails->removeElement($receiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($receiveDetail->getReceiveHeader() === $this) {
                $receiveDetail->setReceiveHeader(null);
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

    public function getPurchaseOrderPaperHeader(): ?PurchaseOrderPaperHeader
    {
        return $this->purchaseOrderPaperHeader;
    }

    public function setPurchaseOrderPaperHeader(?PurchaseOrderPaperHeader $purchaseOrderPaperHeader): self
    {
        $this->purchaseOrderPaperHeader = $purchaseOrderPaperHeader;

        return $this;
    }

    public function getPurchaseOrderCodeNumberOrdinal(): ?int
    {
        return $this->purchaseOrderCodeNumberOrdinal;
    }

    public function setPurchaseOrderCodeNumberOrdinal(int $purchaseOrderCodeNumberOrdinal): self
    {
        $this->purchaseOrderCodeNumberOrdinal = $purchaseOrderCodeNumberOrdinal;

        return $this;
    }

    public function getPurchaseOrderCodeNumberMonth(): ?int
    {
        return $this->purchaseOrderCodeNumberMonth;
    }

    public function setPurchaseOrderCodeNumberMonth(int $purchaseOrderCodeNumberMonth): self
    {
        $this->purchaseOrderCodeNumberMonth = $purchaseOrderCodeNumberMonth;

        return $this;
    }

    public function getPurchaseOrderCodeNumberYear(): ?int
    {
        return $this->purchaseOrderCodeNumberYear;
    }

    public function setPurchaseOrderCodeNumberYear(int $purchaseOrderCodeNumberYear): self
    {
        $this->purchaseOrderCodeNumberYear = $purchaseOrderCodeNumberYear;

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

    /**
     * @return Collection<int, PurchaseInvoiceHeader>
     */
    public function getPurchaseInvoiceHeaders(): Collection
    {
        return $this->purchaseInvoiceHeaders;
    }

    public function addPurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader): self
    {
        if (!$this->purchaseInvoiceHeaders->contains($purchaseInvoiceHeader)) {
            $this->purchaseInvoiceHeaders->add($purchaseInvoiceHeader);
            $purchaseInvoiceHeader->setReceiveHeader($this);
        }

        return $this;
    }

    public function removePurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader): self
    {
        if ($this->purchaseInvoiceHeaders->removeElement($purchaseInvoiceHeader)) {
            // set the owning side to null (unless already changed)
            if ($purchaseInvoiceHeader->getReceiveHeader() === $this) {
                $purchaseInvoiceHeader->setReceiveHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PurchaseReturnHeader>
     */
    public function getPurchaseReturnHeaders(): Collection
    {
        return $this->purchaseReturnHeaders;
    }

    public function addPurchaseReturnHeader(PurchaseReturnHeader $purchaseReturnHeader): self
    {
        if (!$this->purchaseReturnHeaders->contains($purchaseReturnHeader)) {
            $this->purchaseReturnHeaders->add($purchaseReturnHeader);
            $purchaseReturnHeader->setReceiveHeader($this);
        }

        return $this;
    }

    public function removePurchaseReturnHeader(PurchaseReturnHeader $purchaseReturnHeader): self
    {
        if ($this->purchaseReturnHeaders->removeElement($purchaseReturnHeader)) {
            // set the owning side to null (unless already changed)
            if ($purchaseReturnHeader->getReceiveHeader() === $this) {
                $purchaseReturnHeader->setReceiveHeader(null);
            }
        }

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
}
