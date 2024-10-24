<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Grid\Report\SupplierPurchaseOrderMaterialGridType;
use App\Repository\Master\SupplierRepository;
use App\Repository\Purchase\PurchaseOrderHeaderRepository;
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

#[Route('/report/supplier_purchase_order_material')]
class SupplierPurchaseOrderMaterialController extends AbstractController
{
    #[Route('/_list', name: 'app_report_supplier_purchase_order_material__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, SupplierRepository $supplierRepository, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'purchaseOrderHeader:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SupplierPurchaseOrderMaterialGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $suppliers) = $supplierRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT s.id FROM " . PurchaseOrderHeader::class . " s WHERE {$alias} = s.supplier AND s.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['purchaseOrderHeader:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['purchaseOrderHeader:transactionDate'][2]);
            $qb->addOrderBy("{$alias}.id", 'ASC');
        });
        $purchaseOrderHeaders = $this->getPurchaseOrderHeaders($purchaseOrderHeaderRepository, $criteria, $suppliers);

        if ($request->request->has('export')) {
            return $this->export($form, $suppliers, $purchaseOrderHeaders);
        } else {
            return $this->renderForm("report/supplier_purchase_order_material/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'suppliers' => $suppliers,
                'purchaseOrderHeaders' => $purchaseOrderHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_supplier_purchase_order_material_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/supplier_purchase_order_material/index.html.twig");
    }

    private function getPurchaseOrderHeaders(PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository, DataCriteria $criteria, array $suppliers): array
    {
        $startDate = $criteria->getFilter()['purchaseOrderHeader:transactionDate'][1];
        $endDate = $criteria->getFilter()['purchaseOrderHeader:transactionDate'][2];
        $supplierPurchaseOrderHeaders = $purchaseOrderHeaderRepository->findSupplierPurchaseOrderHeaders($suppliers, $startDate, $endDate);
        $purchaseOrderHeaders = [];
        foreach ($supplierPurchaseOrderHeaders as $supplierPurchaseOrderHeader) {
            $purchaseOrderHeaders[$supplierPurchaseOrderHeader->getSupplier()->getId()][] = $supplierPurchaseOrderHeader;
        }

        return $purchaseOrderHeaders;
    }

    public function export(FormInterface $form, array $suppliers, array $purchaseOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/supplier_purchase_order_material/_list_export.html.twig", [
            'form' => $form->createView(),
            'suppliers' => $suppliers,
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'purchase_order_material_per_supplier.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
