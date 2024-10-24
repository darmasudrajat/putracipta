<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\WorkOrderDistributionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderDistributionRepository::class)]
#[ORM\Table(name: 'master_work_order_distribution')]
class WorkOrderDistribution extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'workOrderDistribution', targetEntity: DesignCodeDistributionDetail::class)]
    private Collection $designCodeDistributionDetails;

    #[ORM\Column(length: 20)]
    private ?string $memoConstantName = '';

    public function __construct()
    {
        $this->designCodeDistributionDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, DesignCodeDistributionDetail>
     */
    public function getDesignCodeDistributionDetails(): Collection
    {
        return $this->designCodeDistributionDetails;
    }

    public function addDesignCodeDistributionDetail(DesignCodeDistributionDetail $designCodeDistributionDetail): self
    {
        if (!$this->designCodeDistributionDetails->contains($designCodeDistributionDetail)) {
            $this->designCodeDistributionDetails->add($designCodeDistributionDetail);
            $designCodeDistributionDetail->setWorkOrderDistribution($this);
        }

        return $this;
    }

    public function removeDesignCodeDistributionDetail(DesignCodeDistributionDetail $designCodeDistributionDetail): self
    {
        if ($this->designCodeDistributionDetails->removeElement($designCodeDistributionDetail)) {
            // set the owning side to null (unless already changed)
            if ($designCodeDistributionDetail->getWorkOrderDistribution() === $this) {
                $designCodeDistributionDetail->setWorkOrderDistribution(null);
            }
        }

        return $this;
    }

    public function getMemoConstantName(): ?string
    {
        return $this->memoConstantName;
    }

    public function setMemoConstantName(string $memoConstantName): self
    {
        $this->memoConstantName = $memoConstantName;

        return $this;
    }
}
