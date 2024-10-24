<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DielineMillar;
use App\Entity\Master\DielineMillarDetail;
use App\Entity\Support\Idempotent;
use App\Repository\Master\DielineMillarRepository;
use App\Repository\Master\DielineMillarDetailRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DielineMillarFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private DielineMillarRepository $dielineMillarRepository;
    private DielineMillarDetailRepository $dielineMillarDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->dielineMillarRepository = $entityManager->getRepository(DielineMillar::class);
        $this->dielineMillarDetailRepository = $entityManager->getRepository(DielineMillarDetail::class);
    }

    public function initialize(DielineMillar $dielineMillar, array $options = []): void
    {
        list($datetime) = [$options['datetime']];

        if (empty($dielineMillar->getId())) {
            $dielineMillar->setDate($datetime);
        }
    }

    public function finalize(DielineMillar $dielineMillar, array $options = []): void
    {
        $dielineMillarDetails = $dielineMillar->getDielineMillarDetails();
        if ($dielineMillarDetails[0] !== null) {
            $product = $dielineMillarDetails[0]->getProduct();
            $dielineMillar->setCode($product->getCode());
            $dielineMillar->setName($product->getName());
        }
    }

    public function save(DielineMillar $dielineMillar, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        if ($options['sourceDielineMillar'] !== null) {
            $this->dielineMillarRepository->add($options['sourceDielineMillar']);
        }
        $this->dielineMillarRepository->add($dielineMillar);
        foreach ($dielineMillar->getDielineMillarDetails() as $dielineMillarDetail) {
            $this->dielineMillarDetailRepository->add($dielineMillarDetail);
        }
        $this->entityManager->flush();
    }
    
    public function copyFrom(DielineMillar $sourceDielineMillar): DielineMillar
    {
        $dielineMillar = new DielineMillar();
        $dielineMillar->setQuantity($sourceDielineMillar->getQuantity());
        $dielineMillar->setQuantityUpPrinting($sourceDielineMillar->getQuantityUpPrinting());
        $dielineMillar->setPrintingLayout($sourceDielineMillar->getPrintingLayout());
        $dielineMillar->setNote($sourceDielineMillar->getNote());
        $dielineMillar->setCustomer($sourceDielineMillar->getCustomer());
        $dielineMillar->setCode($sourceDielineMillar->getCode());
        foreach ($sourceDielineMillar->getDielineMillarDetails() as $sourceDielineMillarDetail) {
            $dielineMillarDetail = new DielineMillarDetail();
            $dielineMillarDetail->setProduct($sourceDielineMillarDetail->getProduct());
            $dielineMillar->addDielineMillarDetail($dielineMillarDetail);
        }
        return $dielineMillar;
    }
}
