<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Purchase\PurchaseRequestDetail;
use App\Grid\Shared\InventoryRequestMaterialDetailGridType;
use App\Repository\Stock\InventoryRequestMaterialDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/inventory_request_material_detail')]
class InventoryRequestMaterialDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_inventory_request_material_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryRequestMaterialDetailRepository $inventoryRequestMaterialDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(InventoryRequestMaterialDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryRequestMaterialDetails) = $inventoryRequestMaterialDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->innerJoin("{$alias}.inventoryRequestHeader", 'h');
            $qb->innerJoin("{$alias}.material", 'p');
            $qb->addOrderBy('h.pickupDate', 'DESC');
            if ($request->request->has('purchase_request_header')) {
                $sub = $new(PurchaseRequestDetail::class, 'q');
                $sub->andWhere("IDENTITY(q.inventoryRequestMaterialDetail) = {$alias}.id");
                $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            } else if ($request->request->has('inventory_release_header')) {
                $qb->andWhere("{$alias}.quantityRemaining > 0");                
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:codeNumberOrdinal']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'h', 'codeNumberOrdinal', $request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:codeNumberOrdinal']);
                $add['sort']($qb, 'h', 'codeNumberOrdinal', $request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:codeNumberMonth']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'h', 'codeNumberMonth', $request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:codeNumberMonth']);
                $add['sort']($qb, 'h', 'codeNumberMonth', $request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:codeNumberYear']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:codeNumberYear'])) {
                $add['filter']($qb, 'h', 'codeNumberYear', $request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:codeNumberYear']);
                $add['sort']($qb, 'h', 'codeNumberYear', $request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:codeNumberYear']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:pickupDate']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:pickupDate'])) {
                $add['filter']($qb, 'h', 'pickupDate', $request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:pickupDate']);
                $add['sort']($qb, 'h', 'pickupDate', $request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:pickupDate']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:warehouse']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:warehouse'])) {
                $add['filter']($qb, 'h', 'warehouse', $request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestMaterialHeader:warehouse']);
                $add['sort']($qb, 'h', 'warehouse', $request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestMaterialHeader:warehouse']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['material:code']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['material:code'])) {
                $add['filter']($qb, 'p', 'code', $request->request->get('inventory_request_material_detail_grid')['filter']['material:code']);
                $add['sort']($qb, 'p', 'code', $request->request->get('inventory_request_material_detail_grid')['sort']['material:code']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['material:name']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['material:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('inventory_request_material_detail_grid')['filter']['material:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('inventory_request_material_detail_grid')['sort']['material:name']);
            }
        });

        return $this->renderForm("shared/inventory_request_material_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryRequestMaterialDetails' => $inventoryRequestMaterialDetails,
        ]);
    }
}
