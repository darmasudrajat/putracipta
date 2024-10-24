<?php

namespace App\Entity\Production;

use App\Entity\Master\Customer;
use App\Entity\ProductionHeader;
use App\Repository\Production\QualityControlSortingHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QualityControlSortingHeaderRepository::class)]
#[ORM\Table(name: 'production_quality_control_sorting_header')]
class QualityControlSortingHeader extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'QCS';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\ManyToOne]
    private ?Customer $customer = null;

    #[ORM\Column(length: 200)]
    private ?string $employeeInCharge = '';

    #[ORM\OneToMany(mappedBy: 'qualityControlSortingHeader', targetEntity: QualityControlSortingDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $qualityControlSortingDetails;

    public function __construct()
    {
        $this->qualityControlSortingDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMasterOrderHeader(): ?MasterOrderHeader
    {
        return $this->masterOrderHeader;
    }

    public function setMasterOrderHeader(?MasterOrderHeader $masterOrderHeader): self
    {
        $this->masterOrderHeader = $masterOrderHeader;

        return $this;
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

    public function getEmployeeInCharge(): ?string
    {
        return $this->employeeInCharge;
    }

    public function setEmployeeInCharge(string $employeeInCharge): self
    {
        $this->employeeInCharge = $employeeInCharge;

        return $this;
    }

    /**
     * @return Collection<int, QualityControlSortingDetail>
     */
    public function getQualityControlSortingDetails(): Collection
    {
        return $this->qualityControlSortingDetails;
    }

    public function addQualityControlSortingDetail(QualityControlSortingDetail $qualityControlSortingDetail): self
    {
        if (!$this->qualityControlSortingDetails->contains($qualityControlSortingDetail)) {
            $this->qualityControlSortingDetails->add($qualityControlSortingDetail);
            $qualityControlSortingDetail->setQualityControlSortingHeader($this);
        }

        return $this;
    }

    public function removeQualityControlSortingDetail(QualityControlSortingDetail $qualityControlSortingDetail): self
    {
        if ($this->qualityControlSortingDetails->removeElement($qualityControlSortingDetail)) {
            // set the owning side to null (unless already changed)
            if ($qualityControlSortingDetail->getQualityControlSortingHeader() === $this) {
                $qualityControlSortingDetail->setQualityControlSortingHeader(null);
            }
        }

        return $this;
    }
}
