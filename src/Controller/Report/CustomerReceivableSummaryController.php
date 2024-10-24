<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\SaleInvoiceHeaderGridType;
use App\Repository\Sale\SaleInvoiceHeaderRepository;
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

#[Route('/report/customer_receivable_summary')]
class CustomerReceivableSummaryController extends AbstractController
{
    #[Route('/_list', name: 'app_report_customer_receivable_summary__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleInvoiceHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleInvoiceHeaders) = $saleInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.remainingPayment > 0");
        });

        if ($request->request->has('export')) {
            return $this->export($form, $saleInvoiceHeaders);
        } else {
            return $this->renderForm("report/customer_receivable_summary/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleInvoiceHeaders' => $saleInvoiceHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_customer_receivable_summary_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/customer_receivable_summary/index.html.twig");
    }

    public function export(FormInterface $form, array $saleInvoiceHeaders): Response
    {
        $htmlString = $this->renderView("report/customer_receivable_summary/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleInvoiceHeaders' => $saleInvoiceHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'piutang customer.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
