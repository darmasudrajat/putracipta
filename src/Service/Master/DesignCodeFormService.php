<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DesignCode;
use App\Entity\Master\DesignCodeCheckSheetDetail;
use App\Entity\Master\DesignCodeDistributionDetail;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Entity\Master\DesignCodeProductDetail;
use App\Entity\Support\Idempotent;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Master\DesignCodeCheckSheetDetailRepository;
use App\Repository\Master\DesignCodeDistributionDetailRepository;
use App\Repository\Master\DesignCodeProcessDetailRepository;
use App\Repository\Master\DesignCodeProductDetailRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DesignCodeFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private DesignCodeRepository $designCodeRepository;
    private DesignCodeCheckSheetDetailRepository $designCodeCheckSheetDetailRepository;
    private DesignCodeDistributionDetailRepository $designCodeDistributionDetailRepository;
    private DesignCodeProcessDetailRepository $designCodeProcessDetailRepository;
    private DesignCodeProductDetailRepository $designCodeProductDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->designCodeRepository = $entityManager->getRepository(DesignCode::class);
        $this->designCodeCheckSheetDetailRepository = $entityManager->getRepository(DesignCodeCheckSheetDetail::class);
        $this->designCodeDistributionDetailRepository = $entityManager->getRepository(DesignCodeDistributionDetail::class);
        $this->designCodeProcessDetailRepository = $entityManager->getRepository(DesignCodeProcessDetail::class);
        $this->designCodeProductDetailRepository = $entityManager->getRepository(DesignCodeProductDetail::class);
    }
    
    public function initialize(DesignCode $designCode, array $options = []): void
    {
        list($datetime, $user, $sourceDesignCode) = [$options['datetime'], $options['user'], $options['sourceDesignCode']];

        if (empty($designCode->getId())) {
            if ($sourceDesignCode !== null) {
                $sourceDesignCode->setStatus(DesignCode::STATUS_NA);
            }
            $designCode->setStatus(DesignCode::STATUS_FA);
            $designCode->setCreatedTransactionDateTime($datetime);
            $designCode->setCreatedTransactionUser($user);
        } else {
            $designCode->setModifiedTransactionDateTime($datetime);
            $designCode->setModifiedTransactionUser($user);
        }
    }

    public function finalize(DesignCode $designCode, array $options = []): void
    {
        $productCodeList = array();
        $productNameList = array();
        $designCodeProductList = [];
        foreach ($designCode->getDesignCodeProductDetails() as $designCodeProductDetail) {
            if ($designCodeProductDetail->isIsInactive() === false) {
                $product = $designCodeProductDetail->getProduct();
                $productCodeList[] = $product->getCode();
                $productNameList[] = $product->getName();
                $designCodeProductList[] = $product->getName();
            }
        }
        $designCode->setCode(implode(', ', $productCodeList));
        $designCode->setName(implode(', ', $productNameList));
        
        $designCodeProductUniqueList = array_unique(explode(', ', implode(', ', $designCodeProductList)));
        $designCode->setDesignCodeProductList(implode(', ', $designCodeProductUniqueList));
    }

    public function save(DesignCode $designCode, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        if ($options['sourceDesignCode'] !== null) {
            $this->designCodeRepository->add($options['sourceDesignCode']);
        }
        $this->designCodeRepository->add($designCode);
        foreach ($designCode->getDesignCodeProcessDetails() as $designCodeProcessDetail) {
            $this->designCodeProcessDetailRepository->add($designCodeProcessDetail);
        }
        foreach ($designCode->getDesignCodeDistributionDetails() as $designCodeDistributionDetail) {
            $this->designCodeDistributionDetailRepository->add($designCodeDistributionDetail);
        }
        foreach ($designCode->getDesignCodeCheckSheetDetails() as $designCodeCheckSheetDetail) {
            $this->designCodeCheckSheetDetailRepository->add($designCodeCheckSheetDetail);
        }
        foreach ($designCode->getDesignCodeProductDetails() as $designCodeProductDetail) {
            $this->designCodeProductDetailRepository->add($designCodeProductDetail);
        }
        $this->entityManager->flush();
    }

    public function copyFrom(DesignCode $sourceDesignCode): DesignCode
    {
        $designCode = new DesignCode();
        $designCode->setName($sourceDesignCode->getName());
        $designCode->setVariant($sourceDesignCode->getVariant());
        $designCode->setCustomer($sourceDesignCode->getCustomer());
        $designCode->setColor($sourceDesignCode->getColor());
        $designCode->setPantone($sourceDesignCode->getPantone());
        $designCode->setCoating($sourceDesignCode->getCoating());
        $designCode->setCode($sourceDesignCode->getCode());
        $designCode->setColorSpecial1($sourceDesignCode->getColorSpecial1());
        $designCode->setColorSpecial2($sourceDesignCode->getColorSpecial2());
        $designCode->setColorSpecial3($sourceDesignCode->getColorSpecial3());
        $designCode->setColorSpecial4($sourceDesignCode->getColorSpecial4());
        $designCode->setPrintingUpQuantity($sourceDesignCode->getPrintingUpQuantity());
        $designCode->setPrintingKrisSize($sourceDesignCode->getPrintingKrisSize());
        $designCode->setPaperMountage($sourceDesignCode->getPaperMountage());
        $designCode->setGlossiness($sourceDesignCode->getGlossiness());
        $designCode->setPaperPlanoLength($sourceDesignCode->getPaperPlanoLength());
        $designCode->setPaperPlanoWidth($sourceDesignCode->getPaperPlanoWidth());
        $designCode->setDiecutKnife($sourceDesignCode->getDiecutKnife());
        $designCode->setDielineMillar($sourceDesignCode->getDielineMillar());
        $designCode->setPaperCuttingLength($sourceDesignCode->getPaperCuttingLength());
        $designCode->setPaperCuttingWidth($sourceDesignCode->getPaperCuttingWidth());
        $designCode->setInkCyanPercentage($sourceDesignCode->getInkCyanPercentage());
        $designCode->setInkMagentaPercentage($sourceDesignCode->getInkMagentaPercentage());
        $designCode->setInkYellowPercentage($sourceDesignCode->getInkYellowPercentage());
        $designCode->setInkBlackPercentage($sourceDesignCode->getInkBlackPercentage());
        $designCode->setInkOpvPercentage($sourceDesignCode->getInkOpvPercentage());
        $designCode->setInkK1Percentage($sourceDesignCode->getInkK1Percentage());
        $designCode->setInkK2Percentage($sourceDesignCode->getInkK2Percentage());
        $designCode->setInkK3Percentage($sourceDesignCode->getInkK3Percentage());
        $designCode->setInkK4Percentage($sourceDesignCode->getInkK4Percentage());
        $designCode->setPackagingGlueQuantity($sourceDesignCode->getPackagingGlueQuantity());
        $designCode->setPackagingRubberQuantity($sourceDesignCode->getPackagingRubberQuantity());
        $designCode->setPackagingPaperQuantity($sourceDesignCode->getPackagingPaperQuantity());
        $designCode->setPackagingBoxQuantity($sourceDesignCode->getPackagingBoxQuantity());
        $designCode->setPackagingTapeLargeQuantity($sourceDesignCode->getPackagingTapeLargeQuantity());
        $designCode->setPackagingTapeSmallQuantity($sourceDesignCode->getPackagingTapeSmallQuantity());
        $designCode->setPackagingPlasticQuantity($sourceDesignCode->getPackagingPlasticQuantity());
        $designCode->setPaper($sourceDesignCode->getPaper());
        $designCode->setHotStamping($sourceDesignCode->getHotStamping());
        foreach ($sourceDesignCode->getDesignCodeProductDetails() as $sourceDesignCodeProductDetail) {
            $designCodeProductDetail = new DesignCodeProductDetail();
            $designCodeProductDetail->setProduct($sourceDesignCodeProductDetail->getProduct());
            $designCode->addDesignCodeProductDetail($designCodeProductDetail);
        }
        foreach ($sourceDesignCode->getDesignCodeCheckSheetDetails() as $sourceDesignCodeCheckSheetDetail) {
            $designCodeCheckSheetDetail = new DesignCodeCheckSheetDetail();
            $designCodeCheckSheetDetail->setWorkOrderCheckSheet($sourceDesignCodeCheckSheetDetail->getWorkOrderCheckSheet());
            $designCode->addDesignCodeCheckSheetDetail($designCodeCheckSheetDetail);
        }
        foreach ($sourceDesignCode->getDesignCodeDistributionDetails() as $sourceDesignCodeDistributionDetail) {
            $designCodeDistributionDetail = new DesignCodeDistributionDetail();
            $designCodeDistributionDetail->setWorkOrderDistribution($sourceDesignCodeDistributionDetail->getWorkOrderDistribution());
            $designCode->addDesignCodeDistributionDetail($sourceDesignCodeDistributionDetail);
        }
        foreach ($sourceDesignCode->getDesignCodeProcessDetails() as $sourceDesignCodeProcessDetail) {
            $designCodeProcessDetail = new DesignCodeProcessDetail();
            $designCodeProcessDetail->setWorkOrderProcess($sourceDesignCodeProcessDetail->getWorkOrderProcess());
            $designCode->addDesignCodeProcessDetail($designCodeProcessDetail);
        }
        return $designCode;
    }
}
