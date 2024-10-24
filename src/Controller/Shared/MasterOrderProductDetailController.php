<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\MasterOrderProductDetailGridType;
use App\Repository\Production\MasterOrderProductDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/master_order_product_detail')]
class MasterOrderProductDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_master_order_product_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MasterOrderProductDetailRepository $masterOrderProductDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(MasterOrderProductDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $masterOrderProductDetails) = $masterOrderProductDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $customerId = '';
            $qb->innerJoin("{$alias}.masterOrderHeader", 'h');
            $qb->innerJoin("{$alias}.product", 'p');
            $qb->innerJoin("{$alias}.saleOrderDetail", 'sd');
            $qb->innerJoin("sd.saleOrderHeader", 'sh');
            $qb->innerJoin("sd.unit", 'u');
            
            if (isset($request->request->get('delivery_header')['customer'])) {
                $customerId = $request->request->get('delivery_header')['customer'];
            }
            $qb->andWhere("IDENTITY(h.customer) = :customerId");
            $qb->setParameter('customerId', $customerId);
            
            if (isset($request->request->get('master_order_product_detail_grid')['filter']['saleOrderHeader:referenceNumber']) && isset($request->request->get('master_order_product_detail_grid')['sort']['saleOrderHeader:referenceNumber'])) {
                $add['filter']($qb, 'sh', 'referenceNumber', $request->request->get('master_order_product_detail_grid')['filter']['saleOrderHeader:referenceNumber']);
                $add['sort']($qb, 'sh', 'referenceNumber', $request->request->get('master_order_product_detail_grid')['sort']['saleOrderHeader:referenceNumber']);
            }
            
            if (isset($request->request->get('master_order_product_detail_grid')['filter']['product:code']) && isset($request->request->get('master_order_product_detail_grid')['sort']['product:code'])) {
                $add['filter']($qb, 'p', 'code', $request->request->get('master_order_product_detail_grid')['filter']['product:code']);
                $add['sort']($qb, 'p', 'code', $request->request->get('master_order_product_detail_grid')['sort']['product:code']);
            }
            
            if (isset($request->request->get('master_order_product_detail_grid')['filter']['product:name']) && isset($request->request->get('master_order_product_detail_grid')['sort']['product:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('master_order_product_detail_grid')['filter']['product:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('master_order_product_detail_grid')['sort']['product:name']);
            }
            
            if (isset($request->request->get('master_order_product_detail_grid')['filter']['unit:name']) && isset($request->request->get('master_order_product_detail_grid')['sort']['unit:name'])) {
                $add['filter']($qb, 'u', 'name', $request->request->get('master_order_product_detail_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->request->get('master_order_product_detail_grid')['sort']['unit:name']);
            }
            
            $qb->andWhere("sd.remainingDelivery > 0");
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("sh.transactionStatus IN ('approve', 'partial_delivery')");
        });

        return $this->renderForm("shared/master_order_product_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'masterOrderProductDetails' => $masterOrderProductDetails,
        ]);
    }
}
