<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\SupplierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
#[ORM\Table(name: 'master_supplier')]
class Supplier extends Master
{
    public const WORK_ORDER_DESIGN = 'design';
    public const WORK_ORDER_PRINT = 'print';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $code = '';

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $company = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $address = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $phone = '';

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $email = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $taxNumber = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $note = '';

    #[ORM\ManyToOne(inversedBy: 'suppliers')]
    private ?Account $account = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $fax = '';

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $paymentTerm = 0;

    #[ORM\ManyToOne(inversedBy: 'suppliers')]
    #[Assert\NotNull]
    private ?Currency $currency = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $categoryList = [];

    #[ORM\Column(length: 100)]
    private ?string $name2 = '';

    #[ORM\Column(length: 100)]
    private ?string $name3 = '';

    #[ORM\Column(length: 100)]
    private ?string $name4 = '';

    #[ORM\Column(length: 100)]
    private ?string $name5 = '';

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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(string $taxNumber): self
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getPaymentTerm(): ?int
    {
        return $this->paymentTerm;
    }

    public function setPaymentTerm(int $paymentTerm): self
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCategoryList(): array
    {
        return $this->categoryList;
    }

    public function setCategoryList(array $categoryList): self
    {
        $this->categoryList = $categoryList;

        return $this;
    }

    public function getName2(): ?string
    {
        return $this->name2;
    }

    public function setName2(string $name2): self
    {
        $this->name2 = $name2;

        return $this;
    }

    public function getName3(): ?string
    {
        return $this->name3;
    }

    public function setName3(string $name3): self
    {
        $this->name3 = $name3;

        return $this;
    }

    public function getName4(): ?string
    {
        return $this->name4;
    }

    public function setName4(string $name4): self
    {
        $this->name4 = $name4;

        return $this;
    }

    public function getName5(): ?string
    {
        return $this->name5;
    }

    public function setName5(string $name5): self
    {
        $this->name5 = $name5;

        return $this;
    }
}
