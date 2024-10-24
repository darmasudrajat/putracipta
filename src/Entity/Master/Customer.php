<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'master_customer')]
class Customer extends Master
{
    public const BONDED_ZONE_TRUE = 'berikat';
    public const BONDED_ZONE_FALSE = 'bebas';
    
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

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $addressInvoice = '';

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $paymentTerm = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $isBondedZone = false;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?Currency $currency = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $hasFscCode = false;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery2 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery3 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery4 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery5 = '';

    #[ORM\Column(length: 100)]
    private ?string $name2 = '';

    #[ORM\Column(length: 100)]
    private ?string $name3 = '';

    #[ORM\Column(length: 100)]
    private ?string $name4 = '';

    #[ORM\Column(length: 100)]
    private ?string $name5 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery1 = '';

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: DiecutKnife::class)]
    private Collection $diecutKnives;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: DesignCode::class)]
    private Collection $designCodes;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: DielineMillar::class)]
    private Collection $dielineMillars;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery6 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery7 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery8 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery9 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery10 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery11 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery12 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery13 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery14 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery15 = '';

    #[ORM\Column(length: 100)]
    private ?string $name6 = '';

    #[ORM\Column(length: 100)]
    private ?string $name7 = '';

    #[ORM\Column(length: 100)]
    private ?string $name8 = '';

    #[ORM\Column(length: 100)]
    private ?string $name9 = '';

    #[ORM\Column(length: 100)]
    private ?string $name10 = '';

    #[ORM\Column(length: 100)]
    private ?string $name11 = '';

    #[ORM\Column(length: 100)]
    private ?string $name12 = '';

    #[ORM\Column(length: 100)]
    private ?string $name13 = '';

    #[ORM\Column(length: 100)]
    private ?string $name14 = '';

    #[ORM\Column(length: 100)]
    private ?string $name15 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressTax1 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressTax2 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressTax3 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressTax4 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressTax5 = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $minimumTolerancePercentage = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $maximumTolerancePercentage = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->diecutKnives = new ArrayCollection();
        $this->designCodes = new ArrayCollection();
        $this->dielineMillars = new ArrayCollection();
    }

    public function getIdNameLiteral() 
    {
        return str_pad($this->code, 3, '0', STR_PAD_LEFT) . ' - ' . $this->company;
    }
    
    public function getBondedZoneLiteral()
    {
        return $this->isBondedZone ? self::BONDED_ZONE_TRUE : self::BONDED_ZONE_FALSE;
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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

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

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCustomer($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCustomer() === $this) {
                $product->setCustomer(null);
            }
        }

        return $this;
    }

    public function getAddressInvoice(): ?string
    {
        return $this->addressInvoice;
    }

    public function setAddressInvoice(string $addressInvoice): self
    {
        $this->addressInvoice = $addressInvoice;

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

    public function isIsBondedZone(): ?bool
    {
        return $this->isBondedZone;
    }

    public function setIsBondedZone(bool $isBondedZone): self
    {
        $this->isBondedZone = $isBondedZone;

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

    public function isHasFscCode(): ?bool
    {
        return $this->hasFscCode;
    }

    public function setHasFscCode(bool $hasFscCode): self
    {
        $this->hasFscCode = $hasFscCode;

        return $this;
    }

    public function getAddressDelivery2(): ?string
    {
        return $this->addressDelivery2;
    }

    public function setAddressDelivery2(string $addressDelivery2): self
    {
        $this->addressDelivery2 = $addressDelivery2;

        return $this;
    }

    public function getAddressDelivery3(): ?string
    {
        return $this->addressDelivery3;
    }

    public function setAddressDelivery3(string $addressDelivery3): self
    {
        $this->addressDelivery3 = $addressDelivery3;

        return $this;
    }

    public function getAddressDelivery4(): ?string
    {
        return $this->addressDelivery4;
    }

    public function setAddressDelivery4(string $addressDelivery4): self
    {
        $this->addressDelivery4 = $addressDelivery4;

        return $this;
    }

    public function getAddressDelivery5(): ?string
    {
        return $this->addressDelivery5;
    }

    public function setAddressDelivery5(string $addressDelivery5): self
    {
        $this->addressDelivery5 = $addressDelivery5;

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

    public function getAddressDelivery1(): ?string
    {
        return $this->addressDelivery1;
    }

    public function setAddressDelivery1(string $addressDelivery1): self
    {
        $this->addressDelivery1 = $addressDelivery1;

        return $this;
    }

    /**
     * @return Collection<int, DiecutKnife>
     */
    public function getDiecutKnives(): Collection
    {
        return $this->diecutKnives;
    }

    public function addDiecutKnife(DiecutKnife $diecutKnife): self
    {
        if (!$this->diecutKnives->contains($diecutKnife)) {
            $this->diecutKnives->add($diecutKnife);
            $diecutKnife->setCustomer($this);
        }

        return $this;
    }

    public function removeDiecutKnife(DiecutKnife $diecutKnife): self
    {
        if ($this->diecutKnives->removeElement($diecutKnife)) {
            // set the owning side to null (unless already changed)
            if ($diecutKnife->getCustomer() === $this) {
                $diecutKnife->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DesignCode>
     */
    public function getDesignCodes(): Collection
    {
        return $this->designCodes;
    }

    public function addDesignCode(DesignCode $designCode): self
    {
        if (!$this->designCodes->contains($designCode)) {
            $this->designCodes->add($designCode);
            $designCode->setCustomer($this);
        }

        return $this;
    }

    public function removeDesignCode(DesignCode $designCode): self
    {
        if ($this->designCodes->removeElement($designCode)) {
            // set the owning side to null (unless already changed)
            if ($designCode->getCustomer() === $this) {
                $designCode->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DielineMillar>
     */
    public function getDielineMillars(): Collection
    {
        return $this->dielineMillars;
    }

    public function addDielineMillar(DielineMillar $dielineMillar): self
    {
        if (!$this->dielineMillars->contains($dielineMillar)) {
            $this->dielineMillars->add($dielineMillar);
            $dielineMillar->setCustomer($this);
        }

        return $this;
    }

    public function removeDielineMillar(DielineMillar $dielineMillar): self
    {
        if ($this->dielineMillars->removeElement($dielineMillar)) {
            // set the owning side to null (unless already changed)
            if ($dielineMillar->getCustomer() === $this) {
                $dielineMillar->setCustomer(null);
            }
        }

        return $this;
    }

    public function getAddressDelivery6(): ?string
    {
        return $this->addressDelivery6;
    }

    public function setAddressDelivery6(string $addressDelivery6): self
    {
        $this->addressDelivery6 = $addressDelivery6;

        return $this;
    }

    public function getAddressDelivery7(): ?string
    {
        return $this->addressDelivery7;
    }

    public function setAddressDelivery7(string $addressDelivery7): self
    {
        $this->addressDelivery7 = $addressDelivery7;

        return $this;
    }

    public function getAddressDelivery8(): ?string
    {
        return $this->addressDelivery8;
    }

    public function setAddressDelivery8(string $addressDelivery8): self
    {
        $this->addressDelivery8 = $addressDelivery8;

        return $this;
    }

    public function getAddressDelivery9(): ?string
    {
        return $this->addressDelivery9;
    }

    public function setAddressDelivery9(string $addressDelivery9): self
    {
        $this->addressDelivery9 = $addressDelivery9;

        return $this;
    }

    public function getAddressDelivery10(): ?string
    {
        return $this->addressDelivery10;
    }

    public function setAddressDelivery10(string $addressDelivery10): self
    {
        $this->addressDelivery10 = $addressDelivery10;

        return $this;
    }

    public function getAddressDelivery11(): ?string
    {
        return $this->addressDelivery11;
    }

    public function setAddressDelivery11(string $addressDelivery11): self
    {
        $this->addressDelivery11 = $addressDelivery11;

        return $this;
    }

    public function getAddressDelivery12(): ?string
    {
        return $this->addressDelivery12;
    }

    public function setAddressDelivery12(string $addressDelivery12): self
    {
        $this->addressDelivery12 = $addressDelivery12;

        return $this;
    }

    public function getAddressDelivery13(): ?string
    {
        return $this->addressDelivery13;
    }

    public function setAddressDelivery13(string $addressDelivery13): self
    {
        $this->addressDelivery13 = $addressDelivery13;

        return $this;
    }

    public function getAddressDelivery14(): ?string
    {
        return $this->addressDelivery14;
    }

    public function setAddressDelivery14(string $addressDelivery14): self
    {
        $this->addressDelivery14 = $addressDelivery14;

        return $this;
    }

    public function getAddressDelivery15(): ?string
    {
        return $this->addressDelivery15;
    }

    public function setAddressDelivery15(string $addressDelivery15): self
    {
        $this->addressDelivery15 = $addressDelivery15;

        return $this;
    }

    public function getName6(): ?string
    {
        return $this->name6;
    }

    public function setName6(string $name6): self
    {
        $this->name6 = $name6;

        return $this;
    }

    public function getName7(): ?string
    {
        return $this->name7;
    }

    public function setName7(string $name7): self
    {
        $this->name7 = $name7;

        return $this;
    }

    public function getName8(): ?string
    {
        return $this->name8;
    }

    public function setName8(string $name8): self
    {
        $this->name8 = $name8;

        return $this;
    }

    public function getName9(): ?string
    {
        return $this->name9;
    }

    public function setName9(string $name9): self
    {
        $this->name9 = $name9;

        return $this;
    }

    public function getName10(): ?string
    {
        return $this->name10;
    }

    public function setName10(string $name10): self
    {
        $this->name10 = $name10;

        return $this;
    }

    public function getName11(): ?string
    {
        return $this->name11;
    }

    public function setName11(string $name11): self
    {
        $this->name11 = $name11;

        return $this;
    }

    public function getName12(): ?string
    {
        return $this->name12;
    }

    public function setName12(string $name12): self
    {
        $this->name12 = $name12;

        return $this;
    }

    public function getName13(): ?string
    {
        return $this->name13;
    }

    public function setName13(string $name13): self
    {
        $this->name13 = $name13;

        return $this;
    }

    public function getName14(): ?string
    {
        return $this->name14;
    }

    public function setName14(string $name14): self
    {
        $this->name14 = $name14;

        return $this;
    }

    public function getName15(): ?string
    {
        return $this->name15;
    }

    public function setName15(string $name15): self
    {
        $this->name15 = $name15;

        return $this;
    }

    public function getAddressTax1(): ?string
    {
        return $this->addressTax1;
    }

    public function setAddressTax1(string $addressTax1): self
    {
        $this->addressTax1 = $addressTax1;

        return $this;
    }

    public function getAddressTax2(): ?string
    {
        return $this->addressTax2;
    }

    public function setAddressTax2(string $addressTax2): self
    {
        $this->addressTax2 = $addressTax2;

        return $this;
    }

    public function getAddressTax3(): ?string
    {
        return $this->addressTax3;
    }

    public function setAddressTax3(string $addressTax3): self
    {
        $this->addressTax3 = $addressTax3;

        return $this;
    }

    public function getAddressTax4(): ?string
    {
        return $this->addressTax4;
    }

    public function setAddressTax4(string $addressTax4): self
    {
        $this->addressTax4 = $addressTax4;

        return $this;
    }

    public function getAddressTax5(): ?string
    {
        return $this->addressTax5;
    }

    public function setAddressTax5(string $addressTax5): self
    {
        $this->addressTax5 = $addressTax5;

        return $this;
    }

    public function getMinimumTolerancePercentage(): ?string
    {
        return $this->minimumTolerancePercentage;
    }

    public function setMinimumTolerancePercentage(string $minimumTolerancePercentage): self
    {
        $this->minimumTolerancePercentage = $minimumTolerancePercentage;

        return $this;
    }

    public function getMaximumTolerancePercentage(): ?string
    {
        return $this->maximumTolerancePercentage;
    }

    public function setMaximumTolerancePercentage(string $maximumTolerancePercentage): self
    {
        $this->maximumTolerancePercentage = $maximumTolerancePercentage;

        return $this;
    }
}
