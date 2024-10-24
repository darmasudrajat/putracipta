<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\StockTransferHeaderGridType;
use App\Repository\Stock\StockTransferHeaderRepository;
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

#[Route('/report/stock_transfer_header')]
class StockTransferHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_stock_transfer_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function _list(Request $request, StockTransferHeaderRepository $stockTransferHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(StockTransferHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $stockTransferHeaders) = $stockTransferHeaderRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $stockTransferHeaders);
        } else {
            return $this->renderForm("report/stock_transfer_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'stockTransferHeaders' => $stockTransferHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_stock_transfer_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function index(): Response
    {
        return $this->render("report/stock_transfer_header/index.html.twig");
    }

    public function export(FormInterface $form, array $stockTransferHeaders): Response
    {
        $htmlString = $this->renderView("report/stock_transfer_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'stockTransferHeaders' => $stockTransferHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'stok transfer.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
