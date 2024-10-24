<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\SaleOrderHeaderGridType;
use App\Repository\Sale\SaleOrderHeaderRepository;
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

#[Route('/report/sale_order_summary')]
class SaleOrderSummaryController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_order_summary__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function _list(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'orderReceiveDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderHeaders) = $saleOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('sale_order_header_grid')['filter']['customer:company']) && isset($request->request->get('sale_order_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('sale_order_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('sale_order_header_grid')['sort']['customer:company']);
            }
            if (isset($request->request->get('sale_order_header_grid')['filter']['employee:name']) && isset($request->request->get('sale_order_header_grid')['sort']['employee:name'])) {
                $qb->innerJoin("{$alias}.employee", 'm');
                $add['filter']($qb, 'm', 'name', $request->request->get('sale_order_header_grid')['filter']['employee:name']);
                $add['sort']($qb, 'm', 'name', $request->request->get('sale_order_header_grid')['sort']['employee:name']);
            }
            $qb->addOrderBy("{$alias}.orderReceiveDate", 'ASC');
        });

        if ($request->request->has('export')) {
            return $this->export($form, $saleOrderHeaders);
        } else {
            return $this->renderForm("report/sale_order_summary/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleOrderHeaders' => $saleOrderHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_sale_order_summary_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/sale_order_summary/index.html.twig");
    }

    public function export(FormInterface $form, array $saleOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/sale_order_summary/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_order_summary.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
