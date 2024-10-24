<?php

namespace App\Entity\Stock;

use App\Entity\Master\Product;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\StockDetail;
use App\Entity\Production\MasterOrderProductDetail;
use App\Repository\Stock\InventoryProductReceiveDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InventoryProductReceiveDetailRepository::class)]
#[ORM\Table(name: 'stock_inventory_product_receive_detail')]
class InventoryProductReceiveDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveDetails')]
    private ?MasterOrderProductDetail $masterOrderProductDetail = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveDetails')]
    #[Assert\NotNull]
    private ?InventoryProductReceiveHeader $inventoryProductReceiveHeader = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveDetails')]
    private ?SaleOrderDetail $saleOrderDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityBox = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityBoxExtraPieces = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    #[Assert\GreaterThan(0)]
    private ?string $quantityTotalPieces = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $quantityPiecePerBox = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->inventoryProductReceiveHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityTotalPieces()
    {
        return $this->quantityBox * $this->quantityPiecePerBox + $this->quantityBoxExtraPieces;
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

    public function getMasterOrderProductDetail(): ?MasterOrderProductDetail
    {
        return $this->masterOrderProductDetail;
    }

    public function setMasterOrderProductDetail(?MasterOrderProductDetail $masterOrderProductDetail): self
    {
        $this->masterOrderProductDetail = $masterOrderProductDetail;

        return $this;
    }

    public function getInventoryProductReceiveHeader(): ?InventoryProductReceiveHeader
    {
        return $this->inventoryProductReceiveHeader;
    }

    public function setInventoryProductReceiveHeader(?InventoryProductReceiveHeader $inventoryProductReceiveHeader): self
    {
        $this->inventoryProductReceiveHeader = $inventoryProductReceiveHeader;

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

    public function getSaleOrderDetail(): ?SaleOrderDetail
    {
        return $this->saleOrderDetail;
    }

    public function setSaleOrderDetail(?SaleOrderDetail $saleOrderDetail): self
    {
        $this->saleOrderDetail = $saleOrderDetail;

        return $this;
    }

    public function getQuantityBox(): ?string
    {
        return $this->quantityBox;
    }

    public function setQuantityBox(string $quantityBox): self
    {
        $this->quantityBox = $quantityBox;

        return $this;
    }

    public function getQuantityBoxExtraPieces(): ?string
    {
        return $this->quantityBoxExtraPieces;
    }

    public function setQuantityBoxExtraPieces(string $quantityBoxExtraPieces): self
    {
        $this->quantityBoxExtraPieces = $quantityBoxExtraPieces;

        return $this;
    }

    public function getQuantityTotalPieces(): ?string
    {
        return $this->quantityTotalPieces;
    }

    public function setQuantityTotalPieces(string $quantityTotalPieces): self
    {
        $this->quantityTotalPieces = $quantityTotalPieces;

        return $this;
    }

    public function getQuantityPiecePerBox(): ?string
    {
        return $this->quantityPiecePerBox;
    }

    public function setQuantityPiecePerBox(string $quantityPiecePerBox): self
    {
        $this->quantityPiecePerBox = $quantityPiecePerBox;

        return $this;
    }
}
