<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\SalePaymentHeaderGridType;
use App\Repository\Sale\SalePaymentHeaderRepository;
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

#[Route('/report/sale_payment_header')]
class SalePaymentHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_payment_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, SalePaymentHeaderRepository $salePaymentHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SalePaymentHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $salePaymentHeaders) = $salePaymentHeaderRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $salePaymentHeaders);
        } else {
            return $this->renderForm("report/sale_payment_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'salePaymentHeaders' => $salePaymentHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_sale_payment_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/sale_payment_header/index.html.twig");
    }

    public function export(FormInterface $form, array $salePaymentHeaders): Response
    {
        $htmlString = $this->renderView("report/sale_payment_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'salePaymentHeaders' => $salePaymentHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_payment.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
