<?php

namespace App\Entity\Production;

use App\Entity\Master\Product;
use App\Entity\ProductionDetail;
use App\Repository\Production\QualityControlSortingDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QualityControlSortingDetailRepository::class)]
#[ORM\Table(name: 'production_quality_control_sorting_detail')]
class QualityControlSortingDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'qualityControlSortingDetails')]
    private ?MasterOrderProductDetail $masterOrderProductDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityOrder = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantitySorting = '1.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityGood = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $totalQuantityReject = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRejectPrinting = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRejectCoating = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRejectCutting = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRejectDiecutting = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRejectGlueing = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRejectFinishing = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRejectOthers = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityRemaining = '0.00';

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne(inversedBy: 'qualityControlSortingDetails')]
    #[Assert\NotNull]
    private ?QualityControlSortingHeader $qualityControlSortingHeader = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $rejectPercentage = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->qualityControlSortingHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncTotalQuantityReject(): string
    {
        return $this->quantityRejectPrinting + $this->quantityRejectCoating + $this->quantityRejectCutting + $this->quantityRejectDiecutting + $this->quantityRejectGlueing + $this->quantityRejectFinishing + $this->quantityRejectOthers;
    }
    
    public function getSyncTotalQuantitySorting(): string
    {
        return $this->quantityGood + $this->quantityRejectPrinting + $this->quantityRejectCoating + $this->quantityRejectCutting + $this->quantityRejectDiecutting + $this->quantityRejectGlueing + $this->quantityRejectFinishing + $this->quantityRejectOthers;
    }
    
    public function getSyncQuantityRemaining(): string
    {
        return $this->totalQuantitySorting - $this->quantityOrder;
    }
    
    public function getSyncRejectPercentage(): string
    {
        return $this->totalQuantityReject / $this->totalQuantitySorting * 100;
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getMasterOrderProductDetail(): ?MasterOrderProductDetail
    {
        return $this->masterOrderProductDetail;
    }

    public function setMasterOrderProductDetail(?MasterOrderProductDetail $masterOrderProductDetail): self
    {
        $this->masterOrderProductDetail = $masterOrderProductDetail;

        return $this;
    }

    public function getQuantityOrder(): ?string
    {
        return $this->quantityOrder;
    }

    public function setQuantityOrder(string $quantityOrder): self
    {
        $this->quantityOrder = $quantityOrder;

        return $this;
    }

    public function getTotalQuantitySorting(): ?string
    {
        return $this->totalQuantitySorting;
    }

    public function setTotalQuantitySorting(string $totalQuantitySorting): self
    {
        $this->totalQuantitySorting = $totalQuantitySorting;

        return $this;
    }

    public function getQuantityGood(): ?string
    {
        return $this->quantityGood;
    }

    public function setQuantityGood(string $quantityGood): self
    {
        $this->quantityGood = $quantityGood;

        return $this;
    }

    public function getTotalQuantityReject(): ?string
    {
        return $this->totalQuantityReject;
    }

    public function setTotalQuantityReject(string $totalQuantityReject): self
    {
        $this->totalQuantityReject = $totalQuantityReject;

        return $this;
    }

    public function getQuantityRejectPrinting(): ?string
    {
        return $this->quantityRejectPrinting;
    }

    public function setQuantityRejectPrinting(string $quantityRejectPrinting): self
    {
        $this->quantityRejectPrinting = $quantityRejectPrinting;

        return $this;
    }

    public function getQuantityRejectCoating(): ?string
    {
        return $this->quantityRejectCoating;
    }

    public function setQuantityRejectCoating(string $quantityRejectCoating): self
    {
        $this->quantityRejectCoating = $quantityRejectCoating;

        return $this;
    }

    public function getQuantityRejectCutting(): ?string
    {
        return $this->quantityRejectCutting;
    }

    public function setQuantityRejectCutting(string $quantityRejectCutting): self
    {
        $this->quantityRejectCutting = $quantityRejectCutting;

        return $this;
    }

    public function getQuantityRejectDiecutting(): ?string
    {
        return $this->quantityRejectDiecutting;
    }

    public function setQuantityRejectDiecutting(string $quantityRejectDiecutting): self
    {
        $this->quantityRejectDiecutting = $quantityRejectDiecutting;

        return $this;
    }

    public function getQuantityRejectGlueing(): ?string
    {
        return $this->quantityRejectGlueing;
    }

    public function setQuantityRejectGlueing(string $quantityRejectGlueing): self
    {
        $this->quantityRejectGlueing = $quantityRejectGlueing;

        return $this;
    }

    public function getQuantityRejectFinishing(): ?string
    {
        return $this->quantityRejectFinishing;
    }

    public function setQuantityRejectFinishing(string $quantityRejectFinishing): self
    {
        $this->quantityRejectFinishing = $quantityRejectFinishing;

        return $this;
    }

    public function getQuantityRejectOthers(): ?string
    {
        return $this->quantityRejectOthers;
    }

    public function setQuantityRejectOthers(string $quantityRejectOthers): self
    {
        $this->quantityRejectOthers = $quantityRejectOthers;

        return $this;
    }

    public function getQuantityRemaining(): ?string
    {
        return $this->quantityRemaining;
    }

    public function setQuantityRemaining(string $quantityRemaining): self
    {
        $this->quantityRemaining = $quantityRemaining;

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

    public function getQualityControlSortingHeader(): ?QualityControlSortingHeader
    {
        return $this->qualityControlSortingHeader;
    }

    public function setQualityControlSortingHeader(?QualityControlSortingHeader $qualityControlSortingHeader): self
    {
        $this->qualityControlSortingHeader = $qualityControlSortingHeader;

        return $this;
    }

    public function getRejectPercentage(): ?string
    {
        return $this->rejectPercentage;
    }

    public function setRejectPercentage(string $rejectPercentage): self
    {
        $this->rejectPercentage = $rejectPercentage;

        return $this;
    }
}
