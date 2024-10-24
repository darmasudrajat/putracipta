<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Grid\Report\PaperPurchaseOrderGridType;
use App\Repository\Master\PaperRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
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

#[Route('/report/paper_purchase_order')]
class PaperPurchaseOrderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_paper_purchase_order__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, PaperRepository $paperRepository, PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'purchaseOrderPaperHeader:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(PaperPurchaseOrderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $papers) = $paperRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT d.id FROM " . PurchaseOrderPaperDetail::class . " d INNER JOIN " . PurchaseOrderPaperHeader::class . " h WHERE {$alias} = d.paper AND h.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][2]);
            $qb->addOrderBy("{$alias}.name", 'ASC');
        });
        $purchaseOrderPaperDetails = $this->getPurchaseOrderPaperDetails($purchaseOrderPaperDetailRepository, $criteria, $papers);

        if ($request->request->has('export')) {
            return $this->export($form, $papers, $purchaseOrderPaperDetails);
        } else {
            return $this->renderForm("report/paper_purchase_order/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'papers' => $papers,
                'purchaseOrderPaperDetails' => $purchaseOrderPaperDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_paper_purchase_order_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/paper_purchase_order/index.html.twig");
    }

    private function getPurchaseOrderPaperDetails(PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository, DataCriteria $criteria, array $papers): array
    {
        $startDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][1];
        $endDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][2];
        $paperPurchaseOrderPaperDetails = $purchaseOrderPaperDetailRepository->findPaperPurchaseOrderPaperDetails($papers, $startDate, $endDate);
        $purchaseOrderPaperDetails = [];
        foreach ($paperPurchaseOrderPaperDetails as $paperPurchaseOrderPaperDetail) {
            $purchaseOrderPaperDetails[$paperPurchaseOrderPaperDetail->getPaper()->getId()][] = $paperPurchaseOrderPaperDetail;
        }

        return $purchaseOrderPaperDetails;
    }

    public function export(FormInterface $form, array $papers, array $purchaseOrderPaperDetails): Response
    {
        $htmlString = $this->renderView("report/paper_purchase_order/_list_export.html.twig", [
            'form' => $form->createView(),
            'papers' => $papers,
            'purchaseOrderPaperDetails' => $purchaseOrderPaperDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'purchase_order_per_paper.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
