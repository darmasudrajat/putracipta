<?php

namespace App\Service\Sale;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Admin\LiteralConfig;
use App\Entity\Sale\SaleInvoiceHeader;
use App\Entity\Sale\SalePaymentDetail;
use App\Entity\Sale\SalePaymentHeader;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Sale\SalePaymentDetailRepository;
use App\Repository\Sale\SalePaymentHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Sale\SalePaymentHeaderFormSupport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SalePaymentHeaderFormService
{
    use SalePaymentHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private SalePaymentHeaderRepository $salePaymentHeaderRepository;
    private SalePaymentDetailRepository $salePaymentDetailRepository;
    private LiteralConfigRepository $literalConfigRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->salePaymentHeaderRepository = $entityManager->getRepository(SalePaymentHeader::class);
        $this->salePaymentDetailRepository = $entityManager->getRepository(SalePaymentDetail::class);
        $this->literalConfigRepository = $entityManager->getRepository(LiteralConfig::class);
    }

    public function initialize(SalePaymentHeader $salePaymentHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($salePaymentHeader->getId())) {
            $salePaymentHeader->setCreatedTransactionDateTime($datetime);
            $salePaymentHeader->setCreatedTransactionUser($user);
        } else {
            $salePaymentHeader->setModifiedTransactionDateTime($datetime);
            $salePaymentHeader->setModifiedTransactionUser($user);
        }
        
        $salePaymentHeader->setCodeNumberVersion($salePaymentHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(SalePaymentHeader $salePaymentHeader, array $options = []): void
    {
        if ($salePaymentHeader->getTransactionDate() !== null && $salePaymentHeader->getId() === null) {
            $year = $salePaymentHeader->getTransactionDate()->format('y');
            $month = $salePaymentHeader->getTransactionDate()->format('m');
            $lastSalePaymentHeader = $this->salePaymentHeaderRepository->findRecentBy($year);
            $currentSalePaymentHeader = ($lastSalePaymentHeader === null) ? $salePaymentHeader : $lastSalePaymentHeader;
            $salePaymentHeader->setCodeNumberToNext($currentSalePaymentHeader->getCodeNumber(), $year, $month);

        }
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $salePaymentDetail->setIsCanceled($salePaymentDetail->getSyncIsCanceled());
            $saleInvoiceHeader = $salePaymentDetail->getSaleInvoiceHeader();
            $oldSalePaymentDetails = $this->salePaymentDetailRepository->findBySaleInvoiceHeader($saleInvoiceHeader);
            $totalPayment = '0.00';
            foreach ($oldSalePaymentDetails as $oldSalePaymentDetail) {
                if ($oldSalePaymentDetail->getId() !== $salePaymentDetail->getId()) {
                    $totalPayment += $oldSalePaymentDetail->getAmount();
                }
            }
            $totalPayment += $salePaymentDetail->getAmount();
            $saleInvoiceHeader->setTotalPayment($totalPayment);
            $saleInvoiceHeader->setRemainingPayment($saleInvoiceHeader->getSyncRemainingPayment());
            $remainingTolerance = $this->literalConfigRepository->findLiteralValue('paymentRemainingTolerance');
            if ($saleInvoiceHeader->getRemainingPayment() > $remainingTolerance) {
                $saleInvoiceHeader->setTransactionStatus(SaleInvoiceHeader::TRANSACTION_STATUS_PARTIAL_PAYMENT);
            } else {
                $saleInvoiceHeader->setTransactionStatus(SaleInvoiceHeader::TRANSACTION_STATUS_FULL_PAYMENT);
            }
            
            if ($salePaymentDetail->getServiceTaxMode() !== $salePaymentDetail::SERVICE_TAX_MODE_NON_TAX) {
                $salePaymentDetail->setServiceTaxPercentage($options['serviceTaxPercentage']);
            } else {
                $salePaymentDetail->setServiceTaxPercentage(0);
            }
            $salePaymentDetail->setInvoiceAmount($salePaymentDetail->getSyncInvoiceAmount());
            $salePaymentDetail->setServiceTaxNominal($salePaymentDetail->getSyncServiceTaxNominal());
            $salePaymentDetail->setReceivableAmount($salePaymentDetail->getSyncReceivableAmount());
        }
        $salePaymentHeader->setTotalAmount($salePaymentHeader->getSyncTotalAmount());
        $salePaymentHeader->setTotalReceivable($salePaymentHeader->getSyncTotalReceivable());
        $salePaymentHeader->setReceivedAmount($salePaymentHeader->getSyncReceivedAmount());
        
        $saleOrderReferenceNumberList = [];
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $saleInvoiceHeader = $salePaymentDetail->getSaleInvoiceHeader();
            $saleOrderReferenceNumberList[] = $saleInvoiceHeader->getSaleOrderReferenceNumbers();
        }
        $saleOrderReferenceNumberUniqueList = array_unique(explode(', ', implode(', ', $saleOrderReferenceNumberList)));
        $salePaymentHeader->setSaleOrderReferenceNumbers(implode(', ', $saleOrderReferenceNumberUniqueList));
    }

    public function save(SalePaymentHeader $salePaymentHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($salePaymentHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->salePaymentHeaderRepository->add($salePaymentHeader);
            foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
                $this->salePaymentDetailRepository->add($salePaymentDetail);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($salePaymentHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
}
