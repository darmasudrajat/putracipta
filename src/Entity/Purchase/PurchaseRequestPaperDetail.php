<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\PurchaseDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use App\Repository\Purchase\PurchaseRequestPaperDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRequestPaperDetailRepository::class)]
#[ORM\Table(name: 'purchase_purchase_request_paper_detail')]
class PurchaseRequestPaperDetail extends PurchaseDetail
{
    public const TRANSACTION_STATUS_OPEN = 'open';
    public const TRANSACTION_STATUS_PURCHASE = 'purchase';
    public const TRANSACTION_STATUS_RECEIVE = 'part_rcv';
    public const TRANSACTION_STATUS_CLOSE = 'full_rcv';
    public const TRANSACTION_STATUS_CANCEL = 'cancel';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $usageDate = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseRequestPaperDetails')]
    #[Assert\NotNull]
    private ?PurchaseRequestPaperHeader $purchaseRequestPaperHeader = null;

    #[ORM\Column(length: 60)]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_OPEN;

    #[ORM\OneToMany(mappedBy: 'purchaseRequestPaperDetail', targetEntity: PurchaseOrderPaperDetail::class)]
    private Collection $purchaseOrderPaperDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    #[ORM\ManyToOne(inversedBy: 'purchaseRequestPaperDetails')]
    private ?InventoryRequestPaperDetail $inventoryRequestPaperDetail = null;

    public function __construct()
    {
        $this->purchaseOrderPaperDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseRequestPaperHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsageDate(): ?\DateTimeInterface
    {
        return $this->usageDate;
    }

    public function setUsageDate(?\DateTimeInterface $usageDate): self
    {
        $this->usageDate = $usageDate;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getPurchaseRequestPaperHeader(): ?PurchaseRequestPaperHeader
    {
        return $this->purchaseRequestPaperHeader;
    }

    public function setPurchaseRequestPaperHeader(?PurchaseRequestPaperHeader $purchaseRequestPaperHeader): self
    {
        $this->purchaseRequestPaperHeader = $purchaseRequestPaperHeader;

        return $this;
    }

    public function getTransactionStatus(): ?string
    {
        return $this->transactionStatus;
    }

    public function setTransactionStatus(string $transactionStatus): self
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseOrderPaperDetail>
     */
    public function getPurchaseOrderPaperDetails(): Collection
    {
        return $this->purchaseOrderPaperDetails;
    }

    public function addPurchaseOrderPaperDetail(PurchaseOrderPaperDetail $purchaseOrderPaperDetail): self
    {
        if (!$this->purchaseOrderPaperDetails->contains($purchaseOrderPaperDetail)) {
            $this->purchaseOrderPaperDetails->add($purchaseOrderPaperDetail);
            $purchaseOrderPaperDetail->setPurchaseRequestPaperDetail($this);
        }

        return $this;
    }

    public function removePurchaseOrderPaperDetail(PurchaseOrderPaperDetail $purchaseOrderPaperDetail): self
    {
        if ($this->purchaseOrderPaperDetails->removeElement($purchaseOrderPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseOrderPaperDetail->getPurchaseRequestPaperDetail() === $this) {
                $purchaseOrderPaperDetail->setPurchaseRequestPaperDetail(null);
            }
        }

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

    public function getInventoryRequestPaperDetail(): ?InventoryRequestPaperDetail
    {
        return $this->inventoryRequestPaperDetail;
    }

    public function setInventoryRequestPaperDetail(?InventoryRequestPaperDetail $inventoryRequestPaperDetail): self
    {
        $this->inventoryRequestPaperDetail = $inventoryRequestPaperDetail;

        return $this;
    }
}
