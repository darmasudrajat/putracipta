<?php

namespace App\Entity\Accounting;

use App\Entity\AccountingHeader;
use App\Repository\Accounting\JournalVoucherHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalVoucherHeaderRepository::class)]
#[ORM\Table(name: 'accounting_journal_voucher_header')]
class JournalVoucherHeader extends AccountingHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'journalVoucherHeader', targetEntity: JournalVoucherDetail::class)]
    private Collection $journalVoucherDetails;

    public function __construct()
    {
        $this->journalVoucherDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'JVC';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, JournalVoucherDetail>
     */
    public function getJournalVoucherDetails(): Collection
    {
        return $this->journalVoucherDetails;
    }

    public function addJournalVoucherDetail(JournalVoucherDetail $journalVoucherDetail): self
    {
        if (!$this->journalVoucherDetails->contains($journalVoucherDetail)) {
            $this->journalVoucherDetails->add($journalVoucherDetail);
            $journalVoucherDetail->setJournalVoucherHeader($this);
        }

        return $this;
    }

    public function removeJournalVoucherDetail(JournalVoucherDetail $journalVoucherDetail): self
    {
        if ($this->journalVoucherDetails->removeElement($journalVoucherDetail)) {
            // set the owning side to null (unless already changed)
            if ($journalVoucherDetail->getJournalVoucherHeader() === $this) {
                $journalVoucherDetail->setJournalVoucherHeader(null);
            }
        }

        return $this;
    }
}
