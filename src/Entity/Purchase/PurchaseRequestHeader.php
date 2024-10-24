<?php

namespace App\Entity\Purchase;

use App\Entity\Admin\User;
use App\Entity\Master\Warehouse;
use App\Entity\PurchaseHeader;
use App\Repository\Purchase\PurchaseRequestHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRequestHeaderRepository::class)]
#[ORM\Table(name: 'purchase_purchase_request_header')]
class PurchaseRequestHeader extends PurchaseHeader
{
    public const CODE_NUMBER_CONSTANT = 'PRM';
    public const TRANSACTION_STATUS_DRAFT = 'draft';
    public const TRANSACTION_STATUS_HOLD = 'hold';
    public const TRANSACTION_STATUS_RELEASE = 'release';
    public const TRANSACTION_STATUS_APPROVE = 'approve';
    public const TRANSACTION_STATUS_REJECT = 'reject';
    public const TRANSACTION_STATUS_CANCEL = 'cancelled';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'purchaseRequestHeader', targetEntity: PurchaseRequestDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $purchaseRequestDetails;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvedTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $rejectedTransactionDateTime = null;

    #[ORM\ManyToOne]
    private ?User $approvedTransactionUser = null;

    #[ORM\ManyToOne]
    private ?User $rejectedTransactionUser = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_DRAFT;

    #[ORM\Column]
    private ?bool $isOnHold = false;

    #[ORM\Column(length: 100)]
    private ?string $rejectNote = '';

    #[ORM\Column]
    private ?bool $isViewed = false;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $purchaseRequestMaterialList = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantity = '0.00';

    public function __construct()
    {
        $this->purchaseRequestDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->purchaseRequestDetails as $purchaseRequestDetail) {
            if (!$purchaseRequestDetail->isIsCanceled()) {
                $totalQuantity += $purchaseRequestDetail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, PurchaseRequestDetail>
     */
    public function getPurchaseRequestDetails(): Collection
    {
        return $this->purchaseRequestDetails;
    }

    public function addPurchaseRequestDetail(PurchaseRequestDetail $purchaseRequestDetail): self
    {
        if (!$this->purchaseRequestDetails->contains($purchaseRequestDetail)) {
            $this->purchaseRequestDetails->add($purchaseRequestDetail);
            $purchaseRequestDetail->setPurchaseRequestHeader($this);
        }

        return $this;
    }

    public function removePurchaseRequestDetail(PurchaseRequestDetail $purchaseRequestDetail): self
    {
        if ($this->purchaseRequestDetails->removeElement($purchaseRequestDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseRequestDetail->getPurchaseRequestHeader() === $this) {
                $purchaseRequestDetail->setPurchaseRequestHeader(null);
            }
        }

        return $this;
    }

    public function getApprovedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->approvedTransactionDateTime;
    }

    public function setApprovedTransactionDateTime(?\DateTimeInterface $approvedTransactionDateTime): self
    {
        $this->approvedTransactionDateTime = $approvedTransactionDateTime;

        return $this;
    }

    public function getRejectedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->rejectedTransactionDateTime;
    }

    public function setRejectedTransactionDateTime(?\DateTimeInterface $rejectedTransactionDateTime): self
    {
        $this->rejectedTransactionDateTime = $rejectedTransactionDateTime;

        return $this;
    }

    public function getApprovedTransactionUser(): ?User
    {
        return $this->approvedTransactionUser;
    }

    public function setApprovedTransactionUser(?User $approvedTransactionUser): self
    {
        $this->approvedTransactionUser = $approvedTransactionUser;

        return $this;
    }

    public function getRejectedTransactionUser(): ?User
    {
        return $this->rejectedTransactionUser;
    }

    public function setRejectedTransactionUser(?User $rejectedTransactionUser): self
    {
        $this->rejectedTransactionUser = $rejectedTransactionUser;

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

    public function isIsOnHold(): ?bool
    {
        return $this->isOnHold;
    }

    public function setIsOnHold(bool $isOnHold): self
    {
        $this->isOnHold = $isOnHold;

        return $this;
    }

    public function getRejectNote(): ?string
    {
        return $this->rejectNote;
    }

    public function setRejectNote(string $rejectNote): self
    {
        $this->rejectNote = $rejectNote;

        return $this;
    }

    public function isIsViewed(): ?bool
    {
        return $this->isViewed;
    }

    public function setIsViewed(bool $isViewed): self
    {
        $this->isViewed = $isViewed;

        return $this;
    }

    public function getPurchaseRequestMaterialList(): ?string
    {
        return $this->purchaseRequestMaterialList;
    }

    public function setPurchaseRequestMaterialList(string $purchaseRequestMaterialList): self
    {
        $this->purchaseRequestMaterialList = $purchaseRequestMaterialList;

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
