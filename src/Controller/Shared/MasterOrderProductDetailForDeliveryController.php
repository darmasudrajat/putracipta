<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\MasterOrderProductDetailForDeliveryGridType;
use App\Repository\Master\WarehouseRepository;
use App\Repository\Production\MasterOrderProductDetailRepository;
use App\Repository\Stock\InventoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/master_order_product_detail_for_delivery')]
class MasterOrderProductDetailForDeliveryController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_master_order_product_detail_for_delivery__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MasterOrderProductDetailRepository $masterOrderProductDetailRepository, InventoryRepository $inventoryRepository, WarehouseRepository $warehouseRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(MasterOrderProductDetailForDeliveryGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $masterOrderProductDetails) = $masterOrderProductDetailRepository->fetchData($criteria, function($qb, $alias) use ($request) {
            if (isset($request->request->get('SaleOrderDetail')['product'])) {
                $productId = $request->request->get('SaleOrderDetail')['product'];
            }
            if (isset($productId)) {
                $qb->andWhere("IDENTITY({$alias}.product) = :productId");
                $qb->setParameter('productId', $productId);
            }
            $qb->andWhere("{$alias}.remainingStockDelivery > 0");
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        $saleOrderHeaderData = [
            'referenceNumber' => isset($request->request->get('SaleOrderHeader')['reference_number']) ? $request->request->get('SaleOrderHeader')['reference_number'] : '',
        ];
        $saleOrderDetailData = [
            'id' => isset($request->request->get('SaleOrderDetail')['id']) ? $request->request->get('SaleOrderDetail')['id'] : '',
            'product' => isset($request->request->get('SaleOrderDetail')['product']) ? $request->request->get('SaleOrderDetail')['product'] : '',
            'linePo' => isset($request->request->get('SaleOrderDetail')['line_po']) ? $request->request->get('SaleOrderDetail')['line_po'] : '',
            'quantity' => isset($request->request->get('SaleOrderDetail')['quantity']) ? $request->request->get('SaleOrderDetail')['quantity'] : '',
            'remainingDelivery' => isset($request->request->get('SaleOrderDetail')['remaining_delivery']) ? $request->request->get('SaleOrderDetail')['remaining_delivery'] : '',
            'unitName' => isset($request->request->get('SaleOrderDetail')['unit_name']) ? $request->request->get('SaleOrderDetail')['unit_name'] : '',
        ];

        return $this->renderForm("shared/master_order_product_detail_for_delivery/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'masterOrderProductDetails' => $masterOrderProductDetails,
            'saleOrderHeaderData' => $saleOrderHeaderData,
            'saleOrderDetailData' => $saleOrderDetailData,
            'stockQuantityList' => $this->getStockQuantityList($masterOrderProductDetails, $inventoryRepository, $warehouseRepository),
        ]);
    }
    
    public function getStockQuantityList(array $masterOrderProductDetails, InventoryRepository $inventoryRepository, WarehouseRepository $warehouseRepository): array
    {
        $products = array_map(fn($masterOrderProductDetail) => $masterOrderProductDetail->getProduct(), $masterOrderProductDetails);
        $warehouse = $warehouseRepository->findFinishedGoodsRecord();
        $stockQuantityList = $inventoryRepository->getProductStockQuantityList($warehouse, $products);
        $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'productId');
        
        return $stockQuantityListIndexed;
    }
}
