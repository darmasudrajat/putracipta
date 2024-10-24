<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Material;
use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\PurchaseDetail;
use App\Repository\Purchase\ReceiveDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReceiveDetailRepository::class)]
#[ORM\Table(name: 'purchase_receive_detail')]
class ReceiveDetail extends PurchaseDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'receiveDetails')]
    private ?ReceiveHeader $receiveHeader = null;

    #[ORM\ManyToOne(inversedBy: 'receiveDetails')]
    private ?PurchaseOrderDetail $purchaseOrderDetail = null;

    #[ORM\OneToMany(mappedBy: 'receiveDetail', targetEntity: PurchaseReturnDetail::class)]
    private Collection $purchaseReturnDetails;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $memo = '';

    #[ORM\ManyToOne(inversedBy: 'receiveDetails')]
    private ?PurchaseOrderPaperDetail $purchaseOrderPaperDetail = null;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\OneToMany(mappedBy: 'receiveDetail', targetEntity: PurchaseInvoiceDetail::class)]
    private Collection $purchaseInvoiceDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $receivedQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $remainingQuantity = '0.00';

    public function __construct()
    {
        $this->purchaseReturnDetails = new ArrayCollection();
        $this->purchaseInvoiceDetails = new ArrayCollection();
    }

//    #[Assert\Callback]
//    public function validateQuantityRemaining(ExecutionContextInterface $context, $payload)
//    {
//        if ($this->receiveHeader->getId() === null) {
//            $detailObject = null;
//            if ($this->purchaseOrderDetail !== null && $this->purchaseOrderPaperDetail === null) {
//                $detailObject = $this->purchaseOrderDetail;
//            } else if ($this->purchaseOrderDetail === null && $this->purchaseOrderPaperDetail !== null) {
//                $detailObject = $this->purchaseOrderPaperDetail;
//            }
//            if ($detailObject !== null) {
//                if ($this->receivedQuantity > $detailObject->getRemainingReceive()) {
//                    $context->buildViolation('Quantity must be < remaining')->atPath('receivedQuantity')->addViolation();
//                }
//            }
//        }
//    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->receiveHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getReceiveHeader(): ?ReceiveHeader
    {
        return $this->receiveHeader;
    }

    public function setReceiveHeader(?ReceiveHeader $receiveHeader): self
    {
        $this->receiveHeader = $receiveHeader;

        return $this;
    }

    public function getPurchaseOrderDetail(): ?PurchaseOrderDetail
    {
        return $this->purchaseOrderDetail;
    }

    public function setPurchaseOrderDetail(?PurchaseOrderDetail $purchaseOrderDetail): self
    {
        $this->purchaseOrderDetail = $purchaseOrderDetail;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseReturnDetail>
     */
    public function getPurchaseReturnDetails(): Collection
    {
        return $this->purchaseReturnDetails;
    }

    public function addPurchaseReturnDetail(PurchaseReturnDetail $purchaseReturnDetail): self
    {
        if (!$this->purchaseReturnDetails->contains($purchaseReturnDetail)) {
            $this->purchaseReturnDetails->add($purchaseReturnDetail);
            $purchaseReturnDetail->setReceiveDetail($this);
        }

        return $this;
    }

    public function removePurchaseReturnDetail(PurchaseReturnDetail $purchaseReturnDetail): self
    {
        if ($this->purchaseReturnDetails->removeElement($purchaseReturnDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseReturnDetail->getReceiveDetail() === $this) {
                $purchaseReturnDetail->setReceiveDetail(null);
            }
        }

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

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }

    public function getPurchaseOrderPaperDetail(): ?PurchaseOrderPaperDetail
    {
        return $this->purchaseOrderPaperDetail;
    }

    public function setPurchaseOrderPaperDetail(?PurchaseOrderPaperDetail $purchaseOrderPaperDetail): self
    {
        $this->purchaseOrderPaperDetail = $purchaseOrderPaperDetail;

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

    /**
     * @return Collection<int, PurchaseInvoiceDetail>
     */
    public function getPurchaseInvoiceDetails(): Collection
    {
        return $this->purchaseInvoiceDetails;
    }

    public function addPurchaseInvoiceDetail(PurchaseInvoiceDetail $purchaseInvoiceDetail): self
    {
        if (!$this->purchaseInvoiceDetails->contains($purchaseInvoiceDetail)) {
            $this->purchaseInvoiceDetails->add($purchaseInvoiceDetail);
            $purchaseInvoiceDetail->setReceiveDetail($this);
        }

        return $this;
    }

    public function removePurchaseInvoiceDetail(PurchaseInvoiceDetail $purchaseInvoiceDetail): self
    {
        if ($this->purchaseInvoiceDetails->removeElement($purchaseInvoiceDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseInvoiceDetail->getReceiveDetail() === $this) {
                $purchaseInvoiceDetail->setReceiveDetail(null);
            }
        }

        return $this;
    }

    public function getReceivedQuantity(): ?string
    {
        return $this->receivedQuantity;
    }

    public function setReceivedQuantity(string $receivedQuantity): self
    {
        $this->receivedQuantity = $receivedQuantity;

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
}
