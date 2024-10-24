<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Entity\Purchase\PurchasePaymentDetail;
use App\Entity\Purchase\PurchasePaymentHeader;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Purchase\PurchasePaymentDetailRepository;
use App\Repository\Purchase\PurchasePaymentHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Purchase\PurchasePaymentHeaderFormSupport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchasePaymentHeaderFormService
{
    use PurchasePaymentHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository;
    private PurchasePaymentDetailRepository $purchasePaymentDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->purchasePaymentHeaderRepository = $entityManager->getRepository(PurchasePaymentHeader::class);
        $this->purchasePaymentDetailRepository = $entityManager->getRepository(PurchasePaymentDetail::class);
        $this->purchaseInvoiceHeaderRepository = $entityManager->getRepository(PurchaseInvoiceHeader::class);
    }

    public function initialize(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($purchasePaymentHeader->getId())) {
            $purchasePaymentHeader->setCreatedTransactionDateTime($datetime);
            $purchasePaymentHeader->setCreatedTransactionUser($user);
        } else {
            $purchasePaymentHeader->setModifiedTransactionDateTime($datetime);
            $purchasePaymentHeader->setModifiedTransactionUser($user);
        }
        
        $purchasePaymentHeader->setCodeNumberVersion($purchasePaymentHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
        if ($purchasePaymentHeader->getTransactionDate() !== null && $purchasePaymentHeader->getId() === null) {
            $year = $purchasePaymentHeader->getTransactionDate()->format('y');
            $month = $purchasePaymentHeader->getTransactionDate()->format('m');
            $lastPurchasePaymentHeader = $this->purchasePaymentHeaderRepository->findRecentBy($year, $month);
            $currentPurchasePaymentHeader = ($lastPurchasePaymentHeader === null) ? $purchasePaymentHeader : $lastPurchasePaymentHeader;
            $purchasePaymentHeader->setCodeNumberToNext($currentPurchasePaymentHeader->getCodeNumber(), $year, $month);

        }
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchasePaymentDetail->setIsCanceled($purchasePaymentDetail->getSyncIsCanceled());
        }
        $purchasePaymentHeader->setTotalAmount($purchasePaymentHeader->getSyncTotalAmount());
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchaseInvoiceHeader = $purchasePaymentDetail->getPurchaseInvoiceHeader();
            $oldPurchasePaymentDetails = $this->purchasePaymentDetailRepository->findByPurchaseInvoiceHeader($purchaseInvoiceHeader);
            $totalPayment = '0.00';
            foreach ($oldPurchasePaymentDetails as $oldPurchasePaymentDetail) {
                if ($oldPurchasePaymentDetail->getId() !== $purchasePaymentDetail->getId()) {
                    $totalPayment += $oldPurchasePaymentDetail->getAmount();
                }
            }
            $totalPayment += $purchasePaymentDetail->getAmount();
            $purchaseInvoiceHeader->setTotalPayment($totalPayment);
            $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
            if ($purchaseInvoiceHeader->getRemainingPayment() > '0.00') {
                $purchaseInvoiceHeader->setTransactionStatus(PurchaseInvoiceHeader::TRANSACTION_STATUS_PARTIAL_PAYMENT);
            } else {
                $purchaseInvoiceHeader->setTransactionStatus(PurchaseInvoiceHeader::TRANSACTION_STATUS_FULL_PAYMENT);
            }
        }
        $supplierInvoiceCodeNumberList = [];
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchaseInvoiceHeader = $purchasePaymentDetail->getPurchaseInvoiceHeader();
            $supplierInvoiceCodeNumberList[] = $purchaseInvoiceHeader->getSupplierInvoiceCodeNumber();
        }
        $supplierInvoiceCodeNumberUniqueList = array_unique(explode(', ', implode(', ', $supplierInvoiceCodeNumberList)));
        $purchasePaymentHeader->setSupplierInvoiceCodeNumbers(implode(', ', $supplierInvoiceCodeNumberUniqueList));
    }

    public function save(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($purchasePaymentHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->purchasePaymentHeaderRepository->add($purchasePaymentHeader);
            foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
                $purchaseInvoiceHeader = $purchasePaymentDetail->getPurchaseInvoiceHeader();
                $this->purchasePaymentDetailRepository->add($purchasePaymentDetail);
                $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($purchasePaymentHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
}
