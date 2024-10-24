<?php

namespace App\Entity\Production;

use App\Entity\ProductionDetail;
use App\Repository\Production\WorkOrderCuttingFinishedDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderCuttingFinishedDetailRepository::class)]
#[ORM\Table(name: 'production_work_order_cutting_finished_detail')]
class WorkOrderCuttingFinishedDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workOrderCuttingFinishedDetails')]
    private ?WorkOrderCuttingHeader $workOrderCuttingHeader = null;

    #[ORM\Column]
    private ?int $shiftNumber = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $cuttingDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $cuttingStartTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $cuttingEndTime = null;

    #[ORM\Column]
    private ?int $cuttingQuantityDreek = 0;

    #[ORM\Column]
    private ?int $cuttingQuantityPiece = 0;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->workOrderCuttingHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkOrderCuttingHeader(): ?WorkOrderCuttingHeader
    {
        return $this->workOrderCuttingHeader;
    }

    public function setWorkOrderCuttingHeader(?WorkOrderCuttingHeader $workOrderCuttingHeader): self
    {
        $this->workOrderCuttingHeader = $workOrderCuttingHeader;

        return $this;
    }

    public function getShiftNumber(): ?int
    {
        return $this->shiftNumber;
    }

    public function setShiftNumber(int $shiftNumber): self
    {
        $this->shiftNumber = $shiftNumber;

        return $this;
    }

    public function getCuttingDate(): ?\DateTimeInterface
    {
        return $this->cuttingDate;
    }

    public function setCuttingDate(?\DateTimeInterface $cuttingDate): self
    {
        $this->cuttingDate = $cuttingDate;

        return $this;
    }

    public function getCuttingStartTime(): ?\DateTimeInterface
    {
        return $this->cuttingStartTime;
    }

    public function setCuttingStartTime(?\DateTimeInterface $cuttingStartTime): self
    {
        $this->cuttingStartTime = $cuttingStartTime;

        return $this;
    }

    public function getCuttingEndTime(): ?\DateTimeInterface
    {
        return $this->cuttingEndTime;
    }

    public function setCuttingEndTime(?\DateTimeInterface $cuttingEndTime): self
    {
        $this->cuttingEndTime = $cuttingEndTime;

        return $this;
    }

    public function getCuttingQuantityDreek(): ?int
    {
        return $this->cuttingQuantityDreek;
    }

    public function setCuttingQuantityDreek(int $cuttingQuantityDreek): self
    {
        $this->cuttingQuantityDreek = $cuttingQuantityDreek;

        return $this;
    }

    public function getCuttingQuantityPiece(): ?int
    {
        return $this->cuttingQuantityPiece;
    }

    public function setCuttingQuantityPiece(int $cuttingQuantityPiece): self
    {
        $this->cuttingQuantityPiece = $cuttingQuantityPiece;

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
}
