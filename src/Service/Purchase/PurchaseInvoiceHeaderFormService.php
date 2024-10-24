<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseInvoiceDetail;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Purchase\PurchaseInvoiceDetailRepository;
use App\Repository\Purchase\PurchaseInvoiceHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Purchase\PurchaseInvoiceHeaderFormSupport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseInvoiceHeaderFormService
{
    use PurchaseInvoiceHeaderFormSupport;
    
    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository;
    private PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->purchaseInvoiceHeaderRepository = $entityManager->getRepository(PurchaseInvoiceHeader::class);
        $this->purchaseInvoiceDetailRepository = $entityManager->getRepository(PurchaseInvoiceDetail::class);
    }

    public function initialize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($purchaseInvoiceHeader->getId())) {
            $purchaseInvoiceHeader->setCreatedTransactionDateTime($datetime);
            $purchaseInvoiceHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseInvoiceHeader->setModifiedTransactionDateTime($datetime);
            $purchaseInvoiceHeader->setModifiedTransactionUser($user);
        }
        
        $purchaseInvoiceHeader->setCodeNumberVersion($purchaseInvoiceHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        if ($purchaseInvoiceHeader->getTransactionDate() !== null && $purchaseInvoiceHeader->getId() === null) {
            $year = $purchaseInvoiceHeader->getTransactionDate()->format('y');
            $month = $purchaseInvoiceHeader->getTransactionDate()->format('m');
            $lastPurchaseInvoiceHeader = $this->purchaseInvoiceHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseInvoiceHeader = ($lastPurchaseInvoiceHeader === null) ? $purchaseInvoiceHeader : $lastPurchaseInvoiceHeader;
            $purchaseInvoiceHeader->setCodeNumberToNext($currentPurchaseInvoiceHeader->getCodeNumber(), $year, $month);
        }
        $receiveHeader = $purchaseInvoiceHeader->getReceiveHeader();
        if ($receiveHeader !== null) {
            $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader() === null ? $receiveHeader->getPurchaseOrderPaperHeader() : $receiveHeader->getPurchaseOrderHeader();
            $purchaseInvoiceHeader->setDiscountValueType($purchaseOrderHeader === null ? PurchaseInvoiceHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE : $purchaseOrderHeader->getDiscountValueType());
            $purchaseInvoiceHeader->setDiscountValue($purchaseOrderHeader === null ? '0.00' : $purchaseOrderHeader->getDiscountValue());
            $purchaseInvoiceHeader->setTaxMode($purchaseOrderHeader === null ? PurchaseInvoiceHeader::TAX_MODE_NON_TAX : $purchaseOrderHeader->getTaxMode());
            $purchaseInvoiceHeader->setTaxPercentage($purchaseOrderHeader === null ? 0 : $purchaseOrderHeader->getTaxPercentage());
        }
        
        $purchaseInvoiceHeader->setSupplier($receiveHeader === null ? null : $receiveHeader->getSupplier());
        $purchaseInvoiceHeader->setDueDate($purchaseInvoiceHeader->getSyncDueDate());
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $purchaseInvoiceDetail->setIsCanceled($purchaseInvoiceDetail->getSyncIsCanceled());
        }
        
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $receiveDetail = $purchaseInvoiceDetail->getReceiveDetail();
            $purchaseOrderDetail = empty($receiveDetail->getPurchaseOrderDetail()) ? $receiveDetail->getPurchaseOrderPaperDetail(): $receiveDetail->getPurchaseOrderDetail();
            $purchaseInvoiceDetail->setMaterial($receiveDetail->getMaterial());
            $purchaseInvoiceDetail->setPaper($receiveDetail->getPaper());
            $purchaseInvoiceDetail->setQuantity($receiveDetail->getReceivedQuantity());
            $purchaseInvoiceDetail->setUnitPrice($purchaseOrderDetail->getUnitPriceBeforeTax());
            $purchaseInvoiceDetail->setUnit($receiveDetail === null ? null : $receiveDetail->getUnit());
        }
        
        $purchaseInvoiceHeader->setSubTotal($purchaseInvoiceHeader->getSyncSubTotal());
        $purchaseInvoiceHeader->setTaxNominal($purchaseInvoiceHeader->getSyncTaxNominal());
        $purchaseInvoiceHeader->setGrandTotal($purchaseInvoiceHeader->getSyncGrandTotal());
        
        $purchaseReturnHeaders = $receiveHeader === null ? null : $receiveHeader->getPurchaseReturnHeaders();
        if ($purchaseReturnHeaders !== null) {
            foreach ($purchaseReturnHeaders as $purchaseReturnHeader) {
                if ($purchaseReturnHeader->isIsProductExchange() === false) {
                    $purchaseInvoiceHeader->setTotalReturn($purchaseReturnHeader->getGrandTotal());
                }
            }
        }
        
        $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
    }

    public function save(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($purchaseInvoiceHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader);
            foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
                $this->purchaseInvoiceDetailRepository->add($purchaseInvoiceDetail);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($purchaseInvoiceHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
}
