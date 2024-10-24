<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Stock\Inventory;
use App\Grid\Report\InventoryStockProductGridType;
use App\Repository\Master\ProductRepository;
use App\Repository\Stock\InventoryRepository;
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

#[Route('/report/inventory_stock_summary_product')]
class InventoryStockSummaryProductController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_stock_summary_product__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function _list(Request $request, ProductRepository $productRepository, InventoryRepository $inventoryRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'inventory:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(InventoryStockProductGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $products) = $productRepository->fetchData($criteria, function($qb, $alias, $add) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            if (!empty($criteria->getFilter()['customer:company'][1])) {
                $qb->innerJoin("{$alias}.customer", 'c');
                $add['filter']($qb, 'c', 'company', $criteria->getFilter()['customer:company']);
            }
            $warehouseConditionString = !empty($criteria->getFilter()['inventory:warehouse'][1]) ? 'AND IDENTITY(i.warehouse) = :warehouseId' : '';
            $qb->andWhere("EXISTS (SELECT i.id FROM " . Inventory::class . " i WHERE {$alias} = i.product AND i.isReversed = false AND i.transactionDate <= :endDate {$warehouseConditionString})");
            $qb->setParameter('endDate', $criteria->getFilter()['inventory:transactionDate'][1]);
            if (!empty($criteria->getFilter()['inventory:warehouse'][1])) {
                $qb->setParameter('warehouseId', $criteria->getFilter()['inventory:warehouse'][1]);
            }
            $qb->addOrderBy("{$alias}.id", 'ASC');
        });
        $endingStockList = $this->getEndingStockList($inventoryRepository, $criteria, $products);

        if ($request->request->has('export')) {
            return $this->export($form, $products, $endingStockList);
        } else {
            return $this->renderForm("report/inventory_stock_summary_product/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'products' => $products,
                'endingStockList' => $endingStockList,
            ]);
        }
    }

    #[Route('/', name: 'app_report_inventory_stock_summary_product_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_stock_summary_product/index.html.twig");
    }

    private function getEndingStockList(InventoryRepository $inventoryRepository, DataCriteria $criteria, array $products): array
    {
        $warehouseId = isset($criteria->getFilter()['inventory:warehouse'][1]) ? $criteria->getFilter()['inventory:warehouse'][1] : '';
        $endDate = $criteria->getFilter()['inventory:transactionDate'][1];
        $productEndingStockList = $inventoryRepository->getProductEndingStockList($products, $endDate, $warehouseId);
        $endingStockList = [];
        foreach ($productEndingStockList as $productEndingStockListingStockItem) {
            $endingStockList[$productEndingStockListingStockItem['productId']] = $productEndingStockListingStockItem['endingStock'];
        }

        return $endingStockList;
    }

    public function export(FormInterface $form, array $products, array $endingStockList): Response
    {
        $htmlString = $this->renderView("report/inventory_stock_summary_product/_list_export.html.twig", [
            'form' => $form->createView(),
            'products' => $products,
            'endingStockList' => $endingStockList,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'stok finished goods.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}