<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Grid\Report\MaterialPurchaseOrderGridType;
use App\Repository\Master\MaterialRepository;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
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

#[Route('/report/material_purchase_order')]
class MaterialPurchaseOrderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_material_purchase_order__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, MaterialRepository $materialRepository, PurchaseOrderDetailRepository $purchaseOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'purchaseOrderHeader:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(MaterialPurchaseOrderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $materials) = $materialRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT d.id FROM " . PurchaseOrderDetail::class . " d INNER JOIN " . PurchaseOrderHeader::class . " h WHERE {$alias} = d.material AND h.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['purchaseOrderHeader:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['purchaseOrderHeader:transactionDate'][2]);
            $qb->addOrderBy("{$alias}.name", 'ASC');
        });
        $purchaseOrderDetails = $this->getPurchaseOrderDetails($purchaseOrderDetailRepository, $criteria, $materials);

        if ($request->request->has('export')) {
            return $this->export($form, $materials, $purchaseOrderDetails);
        } else {
            return $this->renderForm("report/material_purchase_order/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'materials' => $materials,
                'purchaseOrderDetails' => $purchaseOrderDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_material_purchase_order_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/material_purchase_order/index.html.twig");
    }

    private function getPurchaseOrderDetails(PurchaseOrderDetailRepository $purchaseOrderDetailRepository, DataCriteria $criteria, array $materials): array
    {
        $startDate = $criteria->getFilter()['purchaseOrderHeader:transactionDate'][1];
        $endDate = $criteria->getFilter()['purchaseOrderHeader:transactionDate'][2];
        $materialPurchaseOrderDetails = $purchaseOrderDetailRepository->findMaterialPurchaseOrderDetails($materials, $startDate, $endDate);
        $purchaseOrderDetails = [];
        foreach ($materialPurchaseOrderDetails as $materialPurchaseOrderDetail) {
            $purchaseOrderDetails[$materialPurchaseOrderDetail->getMaterial()->getId()][] = $materialPurchaseOrderDetail;
        }

        return $purchaseOrderDetails;
    }

    public function export(FormInterface $form, array $materials, array $purchaseOrderDetails): Response
    {
        $htmlString = $this->renderView("report/material_purchase_order/_list_export.html.twig", [
            'form' => $form->createView(),
            'materials' => $materials,
            'purchaseOrderDetails' => $purchaseOrderDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'purchase_order_per_material.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
