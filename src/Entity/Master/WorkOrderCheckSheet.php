<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\WorkOrderCheckSheetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderCheckSheetRepository::class)]
#[ORM\Table(name: 'master_work_order_check_sheet')]
class WorkOrderCheckSheet extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'workOrderCheckSheet', targetEntity: DesignCodeCheckSheetDetail::class)]
    private Collection $designCodeCheckSheetDetails;

    #[ORM\Column(length: 50)]
    private ?string $memoConstantName = '';

    public function __construct()
    {
        $this->designCodeCheckSheetDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, DesignCodeCheckSheetDetail>
     */
    public function getDesignCodeCheckSheetDetails(): Collection
    {
        return $this->designCodeCheckSheetDetails;
    }

    public function addDesignCodeCheckSheetDetail(DesignCodeCheckSheetDetail $designCodeCheckSheetDetail): self
    {
        if (!$this->designCodeCheckSheetDetails->contains($designCodeCheckSheetDetail)) {
            $this->designCodeCheckSheetDetails->add($designCodeCheckSheetDetail);
            $designCodeCheckSheetDetail->setWorkOrderCheckSheet($this);
        }

        return $this;
    }

    public function removeDesignCodeCheckSheetDetail(DesignCodeCheckSheetDetail $designCodeCheckSheetDetail): self
    {
        if ($this->designCodeCheckSheetDetails->removeElement($designCodeCheckSheetDetail)) {
            // set the owning side to null (unless already changed)
            if ($designCodeCheckSheetDetail->getWorkOrderCheckSheet() === $this) {
                $designCodeCheckSheetDetail->setWorkOrderCheckSheet(null);
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
