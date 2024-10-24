<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\AdjustmentStockHeaderGridType;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
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

#[Route('/report/adjustment_stock_header')]
class AdjustmentStockHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_adjustment_stock_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function _list(Request $request, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(AdjustmentStockHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $adjustmentStockHeaders) = $adjustmentStockHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('adjustment_stock_header_grid')['filter']['supplier:company']) && isset($request->request->get('adjustment_stock_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('adjustment_stock_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('adjustment_stock_header_grid')['sort']['supplier:company']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $adjustmentStockHeaders);
        } else {
            return $this->renderForm("report/adjustment_stock_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'adjustmentStockHeaders' => $adjustmentStockHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_adjustment_stock_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function index(): Response
    {
        return $this->render("report/adjustment_stock_header/index.html.twig");
    }

    public function export(FormInterface $form, array $adjustmentStockHeaders): Response
    {
        $htmlString = $this->renderView("report/adjustment_stock_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'adjustmentStockHeaders' => $adjustmentStockHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'penyesuaian stok.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
