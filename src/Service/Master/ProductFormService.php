<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Product;
use App\Entity\Support\Idempotent;
use App\Repository\Master\ProductRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private ProductRepository $productRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->productRepository = $entityManager->getRepository(Product::class);
    }

    public function initialize(Product $product, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($product->getId())) {
            $product->setCreatedTransactionDateTime($datetime);
        } else {
            $product->setModifiedTransactionDateTime($datetime);
        }
    }

    public function finalize(Product $product, array $options = []): void
    {
        if ($options['transactionFile']) {
            $product->setFileExtension($options['transactionFile']->guessExtension());
        }
    }

    public function save(Product $product, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->productRepository->add($product);
        $this->entityManager->flush();
    }

    public function uploadFile(Product $product, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $product->getId() . '.' . $product->getFileExtension();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }
}
