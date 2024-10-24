<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\PurchaseInvoiceHeaderGridType;
use App\Repository\Purchase\PurchaseInvoiceHeaderRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/supplier_payable_summary')]
class SupplierPayableSummaryController extends AbstractController
{
    #[Route('/_list', name: 'app_report_supplier_payable_summary__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(PurchaseInvoiceHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseInvoiceHeaders) = $purchaseInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.remainingPayment > 0");
        });

        if ($request->request->has('export')) {
            return $this->export($form, $purchaseInvoiceHeaders);
        } else {
            return $this->renderForm("report/supplier_payable_summary/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'purchaseInvoiceHeaders' => $purchaseInvoiceHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_supplier_payable_summary_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/supplier_payable_summary/index.html.twig");
    }

    public function export(FormInterface $form, array $purchaseInvoiceHeaders): Response
    {
        $htmlString = $this->renderView("report/supplier_payable_summary/_list_export.html.twig", [
            'form' => $form->createView(),
            'purchaseInvoiceHeaders' => $purchaseInvoiceHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'hutang supplier.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
