<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Stock\InventoryProductReceiveDetail;
use App\Entity\Stock\InventoryProductReceiveHeader;
use App\Grid\Report\ProductInventoryReceiveGridType;
use App\Repository\Master\ProductRepository;
use App\Repository\Stock\InventoryProductReceiveDetailRepository;
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

#[Route('/report/product_inventory_receive')]
class ProductInventoryReceiveController extends AbstractController
{
    #[Route('/_list', name: 'app_report_product_inventory_receive__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function _list(Request $request, ProductRepository $productRepository, InventoryProductReceiveDetailRepository $inventoryProductReceiveDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'inventoryProductReceiveHeader:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(ProductInventoryReceiveGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $products) = $productRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT d.id FROM " . InventoryProductReceiveDetail::class . " d INNER JOIN " . InventoryProductReceiveHeader::class . " h WHERE {$alias} = d.product AND h.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['inventoryProductReceiveHeader:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['inventoryProductReceiveHeader:transactionDate'][2]);
            $qb->addOrderBy("{$alias}.name", 'ASC');
        });
        $inventoryProductReceiveDetails = $this->getInventoryProductReceiveDetails($inventoryProductReceiveDetailRepository, $criteria, $products);

        if ($request->request->has('export')) {
            return $this->export($form, $products, $inventoryProductReceiveDetails);
        } else {
            return $this->renderForm("report/product_inventory_receive/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'products' => $products,
                'inventoryProductReceiveDetails' => $inventoryProductReceiveDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_product_inventory_receive_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function index(): Response
    {
        return $this->render("report/product_inventory_receive/index.html.twig");
    }

    private function getInventoryProductReceiveDetails(InventoryProductReceiveDetailRepository $inventoryProductReceiveDetailRepository, DataCriteria $criteria, array $products): array
    {
        $startDate = $criteria->getFilter()['inventoryProductReceiveHeader:transactionDate'][1];
        $endDate = $criteria->getFilter()['inventoryProductReceiveHeader:transactionDate'][2];
        $productInventoryReceiveDetails = $inventoryProductReceiveDetailRepository->findProductInventoryReceiveDetails($products, $startDate, $endDate);
        $inventoryProductReceiveDetails = [];
        foreach ($productInventoryReceiveDetails as $productInventoryReceiveDetail) {
            $inventoryProductReceiveDetails[$productInventoryReceiveDetail->getProduct()->getId()][] = $productInventoryReceiveDetail;
        }

        return $inventoryProductReceiveDetails;
    }

    public function export(FormInterface $form, array $products, array $inventoryProductReceiveDetails): Response
    {
        $htmlString = $this->renderView("report/product_inventory_receive/_list_export.html.twig", [
            'form' => $form->createView(),
            'products' => $products,
            'inventoryProductReceiveDetails' => $inventoryProductReceiveDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'penerimaan_produksi_per_produk.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
