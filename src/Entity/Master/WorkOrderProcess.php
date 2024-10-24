<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\WorkOrderProcessRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderProcessRepository::class)]
#[ORM\Table(name: 'master_work_order_process')]
class WorkOrderProcess extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'workOrderProcess', targetEntity: DesignCodeProcessDetail::class)]
    private Collection $designCodeProcessDetails;

    public function __construct()
    {
        $this->designCodeProcessDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, DesignCodeProcessDetail>
     */
    public function getDesignCodeProcessDetails(): Collection
    {
        return $this->designCodeProcessDetails;
    }

    public function addDesignCodeProcessDetail(DesignCodeProcessDetail $designCodeProcessDetail): self
    {
        if (!$this->designCodeProcessDetails->contains($designCodeProcessDetail)) {
            $this->designCodeProcessDetails->add($designCodeProcessDetail);
            $designCodeProcessDetail->setWorkOrderProcess($this);
        }

        return $this;
    }

    public function removeDesignCodeProcessDetail(DesignCodeProcessDetail $designCodeProcessDetail): self
    {
        if ($this->designCodeProcessDetails->removeElement($designCodeProcessDetail)) {
            // set the owning side to null (unless already changed)
            if ($designCodeProcessDetail->getWorkOrderProcess() === $this) {
                $designCodeProcessDetail->setWorkOrderProcess(null);
            }
        }

        return $this;
    }
}
