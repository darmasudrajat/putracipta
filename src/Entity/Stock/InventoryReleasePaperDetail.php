<?php

namespace App\Entity\Stock;

use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\StockDetail;
use App\Repository\Stock\InventoryReleasePaperDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InventoryReleasePaperDetailRepository::class)]
#[ORM\Table(name: 'stock_inventory_release_paper_detail')]
class InventoryReleasePaperDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryReleasePaperDetails')]
    #[Assert\NotNull]
    private ?InventoryReleaseHeader $inventoryReleaseHeader = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryReleasePaperDetails')]
    private ?InventoryRequestPaperDetail $inventoryRequestPaperDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('numeric')]
    private ?string $quantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityCurrent = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->inventoryReleaseHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInventoryReleaseHeader(): ?InventoryReleaseHeader
    {
        return $this->inventoryReleaseHeader;
    }

    public function setInventoryReleaseHeader(?InventoryReleaseHeader $inventoryReleaseHeader): self
    {
        $this->inventoryReleaseHeader = $inventoryReleaseHeader;

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
}
