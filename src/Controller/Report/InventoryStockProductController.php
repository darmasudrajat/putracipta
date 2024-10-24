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

#[Route('/report/inventory_stock_product')]
class InventoryStockProductController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_stock_product__list', methods: ['GET', 'POST'])]
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
            $qb->andWhere("EXISTS (SELECT i.id FROM " . Inventory::class . " i WHERE {$alias} = i.product AND i.isReversed = false AND i.transactionDate BETWEEN :startDate AND :endDate {$warehouseConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['inventory:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['inventory:transactionDate'][2]);
            if (!empty($criteria->getFilter()['inventory:warehouse'][1])) {
                $qb->setParameter('warehouseId', $criteria->getFilter()['inventory:warehouse'][1]);
            }
            $qb->addOrderBy("{$alias}.id", 'ASC');
        });
        $beginningStockList = $this->getBeginningStockList($inventoryRepository, $criteria, $products);
        $inventories = $this->getInventories($inventoryRepository, $criteria, $products);

        if ($request->request->has('export')) {
            return $this->export($form, $products, $beginningStockList, $inventories);
        } else {
            return $this->renderForm("report/inventory_stock_product/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'products' => $products,
                'beginningStockList' => $beginningStockList,
                'inventories' => $inventories,
            ]);
        }
    }

    #[Route('/', name: 'app_report_inventory_stock_product_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_stock_product/index.html.twig");
    }

    private function getBeginningStockList(InventoryRepository $inventoryRepository, DataCriteria $criteria, array $products): array
    {
        $warehouseId = isset($criteria->getFilter()['inventory:warehouse'][1]) ? $criteria->getFilter()['inventory:warehouse'][1] : '';
        $startDate = $criteria->getFilter()['inventory:transactionDate'][1];
        $productBeginningStockList = $inventoryRepository->getProductBeginningStockList($products, $startDate, $warehouseId);
        $beginningStockList = [];
        foreach ($productBeginningStockList as $productBeginningStockItem) {
            $beginningStockList[$productBeginningStockItem['productId']] = $productBeginningStockItem['beginningStock'];
        }

        return $beginningStockList;
    }

    private function getInventories(InventoryRepository $inventoryRepository, DataCriteria $criteria, array $products): array
    {
        $warehouseId = isset($criteria->getFilter()['inventory:warehouse'][1]) ? $criteria->getFilter()['inventory:warehouse'][1] : '';
        $startDate = $criteria->getFilter()['inventory:transactionDate'][1];
        $endDate = $criteria->getFilter()['inventory:transactionDate'][2];
        $productInventories = $inventoryRepository->findProductInventories($products, $startDate, $endDate, $warehouseId);
        $inventories = [];
        foreach ($productInventories as $productInventory) {
            $inventories[$productInventory->getProduct()->getId()][] = $productInventory;
        }

        return $inventories;
    }

    public function export(FormInterface $form, array $products, array $beginningStockList, array $inventories): Response
    {
        $htmlString = $this->renderView("report/inventory_stock_product/_list_export.html.twig", [
            'form' => $form->createView(),
            'products' => $products,
            'beginningStockList' => $beginningStockList,
            'inventories' => $inventories,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'mutasi stok finished goods.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}