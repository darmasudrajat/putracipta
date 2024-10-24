<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\ProductDevelopment;
use App\Entity\Production\ProductDevelopmentDetail;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Production\ProductDevelopmentRepository;
use App\Repository\Production\ProductDevelopmentDetailRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Production\ProductDevelopmentFormSupport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductDevelopmentFormService
{
    use ProductDevelopmentFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private ProductDevelopmentRepository $productDevelopmentRepository;
    private ProductDevelopmentDetailRepository $productDevelopmentDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->productDevelopmentRepository = $entityManager->getRepository(ProductDevelopment::class);
        $this->productDevelopmentDetailRepository = $entityManager->getRepository(ProductDevelopmentDetail::class);
    }

    public function initialize(ProductDevelopment $productDevelopment, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($productDevelopment->getId())) {
            $productDevelopment->setCreatedTransactionDateTime($datetime);
            $productDevelopment->setCreatedTransactionUser($user);
        } else {
            $productDevelopment->setModifiedTransactionDateTime($datetime);
            $productDevelopment->setModifiedTransactionUser($user);
        }
    }

    public function finalize(ProductDevelopment $productDevelopment, array $options = []): void
    {
        if ($productDevelopment->getTransactionDate() !== null && $productDevelopment->getId() === null) {
            $year = $productDevelopment->getTransactionDate()->format('y');
            $month = $productDevelopment->getTransactionDate()->format('m');
            $lastProductDevelopment = $this->productDevelopmentRepository->findRecentBy($year, $month);
            $currentProductDevelopment = ($lastProductDevelopment === null) ? $productDevelopment : $lastProductDevelopment;
            $productDevelopment->setCodeNumberToNext($currentProductDevelopment->getCodeNumber(), $year, $month);
        }
        
        $productPrototype = $productDevelopment->getProductPrototype();
        if ($productPrototype !== null) {
            $productDevelopment->setDevelopmentTypeList($productPrototype->getDevelopmentTypeList());
        }
        
        if ($options['transactionFile']) {
            $productDevelopment->setTransactionFileExtension($options['transactionFile']->guessExtension());
        }
        
        $developmentProductList = [];
        foreach ($productDevelopment->getProductDevelopmentDetails() as $productDevelopmentDetail) {
            $product = $productDevelopmentDetail->getProduct();
            $developmentProductList[] = $product->getCode();
        }
        $developmentProductUniqueList = array_unique(explode(', ', implode(', ', $developmentProductList)));
        $productDevelopment->setDevelopmentProductList(implode(', ', $developmentProductUniqueList));
    }

    public function save(ProductDevelopment $productDevelopment, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($productDevelopment) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->productDevelopmentRepository->add($productDevelopment);
            foreach ($productDevelopment->getProductDevelopmentDetails() as $productDevelopmentDetail) {
                $this->productDevelopmentDetailRepository->add($productDevelopmentDetail);
            }
            $this->entityManager->flush();
            
            $transactionLog = $this->buildTransactionLog($productDevelopment);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }

    public function uploadFile(ProductDevelopment $productDevelopment, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $productDevelopment->getFileName();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }
}
