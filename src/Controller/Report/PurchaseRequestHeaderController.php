<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\PurchaseRequestHeaderGridType;
use App\Repository\Purchase\PurchaseRequestHeaderRepository;
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

#[Route('/report/purchase_request_header')]
class PurchaseRequestHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_purchase_request_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(PurchaseRequestHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseRequestHeaders) = $purchaseRequestHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('purchase_request_header_grid')['sort']['warehouse:name'])) {
                $qb->innerJoin("{$alias}.warehouse", 'w');
                $add['sort']($qb, 'w', 'name', $request->request->get('purchase_request_header_grid')['sort']['warehouse:name']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $purchaseRequestHeaders);
        } else {
            return $this->renderForm("report/purchase_request_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'purchaseRequestHeaders' => $purchaseRequestHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_purchase_request_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/purchase_request_header/index.html.twig");
    }

    public function export(FormInterface $form, array $purchaseRequestHeaders): Response
    {
        $htmlString = $this->renderView("report/purchase_request_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'purchaseRequestHeaders' => $purchaseRequestHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'puchase_request_material.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}