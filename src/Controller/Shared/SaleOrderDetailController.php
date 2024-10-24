<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Grid\Shared\SaleOrderDetailGridType;
use App\Repository\Master\WarehouseRepository;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Stock\InventoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/sale_order_detail')]
class SaleOrderDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_sale_order_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SaleOrderDetailRepository $saleOrderDetailRepository, InventoryRepository $inventoryRepository, WarehouseRepository $warehouseRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'deliveryDate' => SortDescending::class,
        ]);
        $form = $this->createForm(SaleOrderDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderDetails) = $saleOrderDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $customerId = '';
            $qb->innerJoin("{$alias}.saleOrderHeader", 's');
            $qb->innerJoin("{$alias}.product", 'p');
            $qb->innerJoin("{$alias}.unit", 'u');
            
            if (isset($request->request->get('delivery_header')['customer'])) {
                $customerId = $request->request->get('delivery_header')['customer'];
            } elseif (isset($request->request->get('master_order_header')['customer'])) {
                $customerId = $request->request->get('master_order_header')['customer'];
            }
            $qb->andWhere("IDENTITY(s.customer) = :customerId");
            $qb->setParameter('customerId', $customerId);
            
            if (isset($request->request->get('sale_order_detail_grid')['filter']['saleOrderHeader:referenceNumber']) && isset($request->request->get('sale_order_detail_grid')['sort']['saleOrderHeader:referenceNumber'])) {
                $add['filter']($qb, 's', 'referenceNumber', $request->request->get('sale_order_detail_grid')['filter']['saleOrderHeader:referenceNumber']);
                $add['sort']($qb, 's', 'referenceNumber', $request->request->get('sale_order_detail_grid')['sort']['saleOrderHeader:referenceNumber']);
            }
            
            if (isset($request->request->get('sale_order_detail_grid')['filter']['product:code']) && isset($request->request->get('sale_order_detail_grid')['sort']['product:code'])) {
                $add['filter']($qb, 'p', 'code', $request->request->get('sale_order_detail_grid')['filter']['product:code']);
                $add['sort']($qb, 'p', 'code', $request->request->get('sale_order_detail_grid')['sort']['product:code']);
            }
            
            if (isset($request->request->get('sale_order_detail_grid')['filter']['product:name']) && isset($request->request->get('sale_order_detail_grid')['sort']['product:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('sale_order_detail_grid')['filter']['product:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('sale_order_detail_grid')['sort']['product:name']);
            }
            
            if (isset($request->request->get('sale_order_detail_grid')['filter']['unit:name']) && isset($request->request->get('sale_order_detail_grid')['sort']['unit:name'])) {
                $add['filter']($qb, 'u', 'name', $request->request->get('sale_order_detail_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->request->get('sale_order_detail_grid')['sort']['unit:name']);
            }
            if ($request->request->has('delivery_header')) {
                $qb->andWhere("{$alias}.remainingQuantityDelivery > {$alias}.minimumToleranceQuantity");
            } else if ($request->request->has('master_order_header')) {
                $qb->andWhere("{$alias}.quantityProductionRemaining > 0");
            }
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isTransactionClosed = false");
            $qb->andWhere("s.transactionStatus IN ('Approve', 'partial_delivery')");
        });

        return $this->renderForm("shared/sale_order_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleOrderDetails' => $saleOrderDetails,
            'stockQuantityList' => $this->getStockQuantityList($saleOrderDetails, $inventoryRepository, $warehouseRepository),
        ]);
    }
    
    public function getStockQuantityList(array $saleOrderDetails, InventoryRepository $inventoryRepository, WarehouseRepository $warehouseRepository): array
    {
        $products = array_map(fn($saleOrderDetail) => $saleOrderDetail->getProduct(), $saleOrderDetails);
        $warehouse = $warehouseRepository->findFinishedGoodsRecord();
        $stockQuantityList = $inventoryRepository->getProductStockQuantityList($warehouse, $products);
        $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'productId');
        
        return $stockQuantityListIndexed;
    }
}
