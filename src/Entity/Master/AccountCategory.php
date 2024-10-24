<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\AccountCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccountCategoryRepository::class)]
#[ORM\Table(name: 'master_account_category')]
class AccountCategory extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $code = '';

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'accountCategories')]
    private ?self $accountCategory = null;

    #[ORM\OneToMany(mappedBy: 'accountCategory', targetEntity: self::class)]
    private Collection $accountCategories;

    #[ORM\OneToMany(mappedBy: 'accountCategory', targetEntity: Account::class)]
    private Collection $accounts;

    public function __construct()
    {
        $this->accountCategories = new ArrayCollection();
        $this->accounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAccountCategory(): ?self
    {
        return $this->accountCategory;
    }

    public function setAccountCategory(?self $accountCategory): self
    {
        $this->accountCategory = $accountCategory;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAccountCategories(): Collection
    {
        return $this->accountCategories;
    }

    public function addAccountCategory(self $accountCategory): self
    {
        if (!$this->accountCategories->contains($accountCategory)) {
            $this->accountCategories->add($accountCategory);
            $accountCategory->setAccountCategory($this);
        }

        return $this;
    }

    public function removeAccountCategory(self $accountCategory): self
    {
        if ($this->accountCategories->removeElement($accountCategory)) {
            // set the owning side to null (unless already changed)
            if ($accountCategory->getAccountCategory() === $this) {
                $accountCategory->setAccountCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setAccountCategory($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getAccountCategory() === $this) {
                $account->setAccountCategory(null);
            }
        }

        return $this;
    }
}
