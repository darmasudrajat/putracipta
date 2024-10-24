<?php

namespace App\Entity\Production;

use App\Entity\ProductionHeader;
use App\Repository\Production\WorkOrderColorMixingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderColorMixingRepository::class)]
#[ORM\Table(name: 'production_work_order_color_mixing')]
class WorkOrderColorMixing extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'WCM';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $paperInseetUsedUsage = null;

    #[ORM\Column]
    private ?int $paperInseetNewUsage = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixFirstOneName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixFirstOneWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixFirstTwoName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixFirstTwoWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixFirstThreeName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixFirstThreeWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixFirstFourName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixFirstFourWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixSecondOneName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixSecondOneWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixSecondTwoName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixSecondTwoWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixSecondThreeName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixSecondThreeWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixSecondFourName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixSecondFourWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixThirdOneName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixThirdOneWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixThirdTwoName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixThirdTwoWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixThirdThreeName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixThirdThreeWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixThirdFourName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixThirdFourWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixFourthOneName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixFourthOneWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixFourthTwoName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixFourthTwoWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixFourthThreeName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixFourthThreeWeight = null;

    #[ORM\Column(length: 100)]
    private ?string $specialColorMixFourthFourName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $specialColorMixFourthFourWeight = null;

    #[ORM\ManyToOne(inversedBy: 'workOrderColorMixings')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaperInseetUsedUsage(): ?int
    {
        return $this->paperInseetUsedUsage;
    }

    public function setPaperInseetUsedUsage(int $paperInseetUsedUsage): self
    {
        $this->paperInseetUsedUsage = $paperInseetUsedUsage;

        return $this;
    }

    public function getPaperInseetNewUsage(): ?int
    {
        return $this->paperInseetNewUsage;
    }

    public function setPaperInseetNewUsage(int $paperInseetNewUsage): self
    {
        $this->paperInseetNewUsage = $paperInseetNewUsage;

        return $this;
    }

    public function getSpecialColorMixFirstOneName(): ?string
    {
        return $this->specialColorMixFirstOneName;
    }

    public function setSpecialColorMixFirstOneName(string $specialColorMixFirstOneName): self
    {
        $this->specialColorMixFirstOneName = $specialColorMixFirstOneName;

        return $this;
    }

    public function getSpecialColorMixFirstOneWeight(): ?string
    {
        return $this->specialColorMixFirstOneWeight;
    }

    public function setSpecialColorMixFirstOneWeight(string $specialColorMixFirstOneWeight): self
    {
        $this->specialColorMixFirstOneWeight = $specialColorMixFirstOneWeight;

        return $this;
    }

    public function getSpecialColorMixFirstTwoName(): ?string
    {
        return $this->specialColorMixFirstTwoName;
    }

    public function setSpecialColorMixFirstTwoName(string $specialColorMixFirstTwoName): self
    {
        $this->specialColorMixFirstTwoName = $specialColorMixFirstTwoName;

        return $this;
    }

    public function getSpecialColorMixFirstTwoWeight(): ?string
    {
        return $this->specialColorMixFirstTwoWeight;
    }

    public function setSpecialColorMixFirstTwoWeight(string $specialColorMixFirstTwoWeight): self
    {
        $this->specialColorMixFirstTwoWeight = $specialColorMixFirstTwoWeight;

        return $this;
    }

    public function getSpecialColorMixFirstThreeName(): ?string
    {
        return $this->specialColorMixFirstThreeName;
    }

    public function setSpecialColorMixFirstThreeName(string $specialColorMixFirstThreeName): self
    {
        $this->specialColorMixFirstThreeName = $specialColorMixFirstThreeName;

        return $this;
    }

    public function getSpecialColorMixFirstThreeWeight(): ?string
    {
        return $this->specialColorMixFirstThreeWeight;
    }

    public function setSpecialColorMixFirstThreeWeight(string $specialColorMixFirstThreeWeight): self
    {
        $this->specialColorMixFirstThreeWeight = $specialColorMixFirstThreeWeight;

        return $this;
    }

    public function getSpecialColorMixFirstFourName(): ?string
    {
        return $this->specialColorMixFirstFourName;
    }

    public function setSpecialColorMixFirstFourName(string $specialColorMixFirstFourName): self
    {
        $this->specialColorMixFirstFourName = $specialColorMixFirstFourName;

        return $this;
    }

    public function getSpecialColorMixFirstFourWeight(): ?string
    {
        return $this->specialColorMixFirstFourWeight;
    }

    public function setSpecialColorMixFirstFourWeight(string $specialColorMixFirstFourWeight): self
    {
        $this->specialColorMixFirstFourWeight = $specialColorMixFirstFourWeight;

        return $this;
    }

    public function getSpecialColorMixSecondOneName(): ?string
    {
        return $this->specialColorMixSecondOneName;
    }

    public function setSpecialColorMixSecondOneName(string $specialColorMixSecondOneName): self
    {
        $this->specialColorMixSecondOneName = $specialColorMixSecondOneName;

        return $this;
    }

    public function getSpecialColorMixSecondOneWeight(): ?string
    {
        return $this->specialColorMixSecondOneWeight;
    }

    public function setSpecialColorMixSecondOneWeight(string $specialColorMixSecondOneWeight): self
    {
        $this->specialColorMixSecondOneWeight = $specialColorMixSecondOneWeight;

        return $this;
    }

    public function getSpecialColorMixSecondTwoName(): ?string
    {
        return $this->specialColorMixSecondTwoName;
    }

    public function setSpecialColorMixSecondTwoName(string $specialColorMixSecondTwoName): self
    {
        $this->specialColorMixSecondTwoName = $specialColorMixSecondTwoName;

        return $this;
    }

    public function getSpecialColorMixSecondTwoWeight(): ?string
    {
        return $this->specialColorMixSecondTwoWeight;
    }

    public function setSpecialColorMixSecondTwoWeight(string $specialColorMixSecondTwoWeight): self
    {
        $this->specialColorMixSecondTwoWeight = $specialColorMixSecondTwoWeight;

        return $this;
    }

    public function getSpecialColorMixSecondThreeName(): ?string
    {
        return $this->specialColorMixSecondThreeName;
    }

    public function setSpecialColorMixSecondThreeName(string $specialColorMixSecondThreeName): self
    {
        $this->specialColorMixSecondThreeName = $specialColorMixSecondThreeName;

        return $this;
    }

    public function getSpecialColorMixSecondThreeWeight(): ?string
    {
        return $this->specialColorMixSecondThreeWeight;
    }

    public function setSpecialColorMixSecondThreeWeight(string $specialColorMixSecondThreeWeight): self
    {
        $this->specialColorMixSecondThreeWeight = $specialColorMixSecondThreeWeight;

        return $this;
    }

    public function getSpecialColorMixSecondFourName(): ?string
    {
        return $this->specialColorMixSecondFourName;
    }

    public function setSpecialColorMixSecondFourName(string $specialColorMixSecondFourName): self
    {
        $this->specialColorMixSecondFourName = $specialColorMixSecondFourName;

        return $this;
    }

    public function getSpecialColorMixSecondFourWeight(): ?string
    {
        return $this->specialColorMixSecondFourWeight;
    }

    public function setSpecialColorMixSecondFourWeight(string $specialColorMixSecondFourWeight): self
    {
        $this->specialColorMixSecondFourWeight = $specialColorMixSecondFourWeight;

        return $this;
    }

    public function getSpecialColorMixThirdOneName(): ?string
    {
        return $this->specialColorMixThirdOneName;
    }

    public function setSpecialColorMixThirdOneName(string $specialColorMixThirdOneName): self
    {
        $this->specialColorMixThirdOneName = $specialColorMixThirdOneName;

        return $this;
    }

    public function getSpecialColorMixThirdOneWeight(): ?string
    {
        return $this->specialColorMixThirdOneWeight;
    }

    public function setSpecialColorMixThirdOneWeight(string $specialColorMixThirdOneWeight): self
    {
        $this->specialColorMixThirdOneWeight = $specialColorMixThirdOneWeight;

        return $this;
    }

    public function getSpecialColorMixThirdTwoName(): ?string
    {
        return $this->specialColorMixThirdTwoName;
    }

    public function setSpecialColorMixThirdTwoName(string $specialColorMixThirdTwoName): self
    {
        $this->specialColorMixThirdTwoName = $specialColorMixThirdTwoName;

        return $this;
    }

    public function getSpecialColorMixThirdTwoWeight(): ?string
    {
        return $this->specialColorMixThirdTwoWeight;
    }

    public function setSpecialColorMixThirdTwoWeight(string $specialColorMixThirdTwoWeight): self
    {
        $this->specialColorMixThirdTwoWeight = $specialColorMixThirdTwoWeight;

        return $this;
    }

    public function getSpecialColorMixThirdThreeName(): ?string
    {
        return $this->specialColorMixThirdThreeName;
    }

    public function setSpecialColorMixThirdThreeName(string $specialColorMixThirdThreeName): self
    {
        $this->specialColorMixThirdThreeName = $specialColorMixThirdThreeName;

        return $this;
    }

    public function getSpecialColorMixThirdThreeWeight(): ?string
    {
        return $this->specialColorMixThirdThreeWeight;
    }

    public function setSpecialColorMixThirdThreeWeight(string $specialColorMixThirdThreeWeight): self
    {
        $this->specialColorMixThirdThreeWeight = $specialColorMixThirdThreeWeight;

        return $this;
    }

    public function getSpecialColorMixThirdFourName(): ?string
    {
        return $this->specialColorMixThirdFourName;
    }

    public function setSpecialColorMixThirdFourName(string $specialColorMixThirdFourName): self
    {
        $this->specialColorMixThirdFourName = $specialColorMixThirdFourName;

        return $this;
    }

    public function getSpecialColorMixThirdFourWeight(): ?string
    {
        return $this->specialColorMixThirdFourWeight;
    }

    public function setSpecialColorMixThirdFourWeight(string $specialColorMixThirdFourWeight): self
    {
        $this->specialColorMixThirdFourWeight = $specialColorMixThirdFourWeight;

        return $this;
    }

    public function getSpecialColorMixFourthOneName(): ?string
    {
        return $this->specialColorMixFourthOneName;
    }

    public function setSpecialColorMixFourthOneName(string $specialColorMixFourthOneName): self
    {
        $this->specialColorMixFourthOneName = $specialColorMixFourthOneName;

        return $this;
    }

    public function getSpecialColorMixFourthOneWeight(): ?string
    {
        return $this->specialColorMixFourthOneWeight;
    }

    public function setSpecialColorMixFourthOneWeight(string $specialColorMixFourthOneWeight): self
    {
        $this->specialColorMixFourthOneWeight = $specialColorMixFourthOneWeight;

        return $this;
    }

    public function getSpecialColorMixFourthTwoName(): ?string
    {
        return $this->specialColorMixFourthTwoName;
    }

    public function setSpecialColorMixFourthTwoName(string $specialColorMixFourthTwoName): self
    {
        $this->specialColorMixFourthTwoName = $specialColorMixFourthTwoName;

        return $this;
    }

    public function getSpecialColorMixFourthTwoWeight(): ?string
    {
        return $this->specialColorMixFourthTwoWeight;
    }

    public function setSpecialColorMixFourthTwoWeight(string $specialColorMixFourthTwoWeight): self
    {
        $this->specialColorMixFourthTwoWeight = $specialColorMixFourthTwoWeight;

        return $this;
    }

    public function getSpecialColorMixFourthThreeName(): ?string
    {
        return $this->specialColorMixFourthThreeName;
    }

    public function setSpecialColorMixFourthThreeName(string $specialColorMixFourthThreeName): self
    {
        $this->specialColorMixFourthThreeName = $specialColorMixFourthThreeName;

        return $this;
    }

    public function getSpecialColorMixFourthThreeWeight(): ?string
    {
        return $this->specialColorMixFourthThreeWeight;
    }

    public function setSpecialColorMixFourthThreeWeight(string $specialColorMixFourthThreeWeight): self
    {
        $this->specialColorMixFourthThreeWeight = $specialColorMixFourthThreeWeight;

        return $this;
    }

    public function getSpecialColorMixFourthFourName(): ?string
    {
        return $this->specialColorMixFourthFourName;
    }

    public function setSpecialColorMixFourthFourName(string $specialColorMixFourthFourName): self
    {
        $this->specialColorMixFourthFourName = $specialColorMixFourthFourName;

        return $this;
    }

    public function getSpecialColorMixFourthFourWeight(): ?string
    {
        return $this->specialColorMixFourthFourWeight;
    }

    public function setSpecialColorMixFourthFourWeight(string $specialColorMixFourthFourWeight): self
    {
        $this->specialColorMixFourthFourWeight = $specialColorMixFourthFourWeight;

        return $this;
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
}
