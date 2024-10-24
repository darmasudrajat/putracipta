<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\SaleReturnHeaderGridType;
use App\Repository\Sale\SaleReturnHeaderRepository;
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

#[Route('/report/sale_return_header')]
class SaleReturnHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_return_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function _list(Request $request, SaleReturnHeaderRepository $saleReturnHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleReturnHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleReturnHeaders) = $saleReturnHeaderRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $saleReturnHeaders);
        } else {
            return $this->renderForm("report/sale_return_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleReturnHeaders' => $saleReturnHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_sale_return_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function index(): Response
    {
        return $this->render("report/sale_return_header/index.html.twig");
    }

    public function export(FormInterface $form, array $saleReturnHeaders): Response
    {
        $htmlString = $this->renderView("report/sale_return_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleReturnHeaders' => $saleReturnHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_return.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
