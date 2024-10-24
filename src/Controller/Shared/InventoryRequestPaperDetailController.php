<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Grid\Shared\InventoryRequestPaperDetailGridType;
use App\Repository\Stock\InventoryRequestPaperDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/inventory_request_paper_detail')]
class InventoryRequestPaperDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_inventory_request_paper_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryRequestPaperDetailRepository $inventoryRequestPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(InventoryRequestPaperDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryRequestPaperDetails) = $inventoryRequestPaperDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->innerJoin("{$alias}.inventoryRequestHeader", 'h');
//            $qb->innerJoin("{$alias}.masterOrderHeader", 'm');
            $qb->innerJoin("{$alias}.paper", 'p');
//            $qb->innerJoin("m.customer", 'c');
            $qb->addOrderBy('h.pickupDate', 'DESC');
            if ($request->request->has('purchase_request_paper_header')) {
                $sub = $new(PurchaseRequestPaperDetail::class, 'q');
                $sub->andWhere("IDENTITY(q.inventoryRequestPaperDetail) = {$alias}.id");
                $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            } else if ($request->request->has('inventory_release_header')) {
                $qb->andWhere("{$alias}.quantityRemaining > 0");                
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:codeNumberOrdinal']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'h', 'codeNumberOrdinal', $request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:codeNumberOrdinal']);
                $add['sort']($qb, 'h', 'codeNumberOrdinal', $request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:codeNumberMonth']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'h', 'codeNumberMonth', $request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:codeNumberMonth']);
                $add['sort']($qb, 'h', 'codeNumberMonth', $request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:codeNumberYear']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:codeNumberYear'])) {
                $add['filter']($qb, 'h', 'codeNumberYear', $request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:codeNumberYear']);
                $add['sort']($qb, 'h', 'codeNumberYear', $request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:codeNumberYear']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:pickupDate']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:pickupDate'])) {
                $add['filter']($qb, 'h', 'pickupDate', $request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:pickupDate']);
                $add['sort']($qb, 'h', 'pickupDate', $request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:pickupDate']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:warehouse']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:warehouse'])) {
                $add['filter']($qb, 'h', 'warehouse', $request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestPaperHeader:warehouse']);
                $add['sort']($qb, 'h', 'warehouse', $request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestPaperHeader:warehouse']);
            }
//            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['masterOrderHeader:codeNumberOrdinal']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['masterOrderHeader:codeNumberOrdinal'])) {
//                $add['filter']($qb, 'm', 'codeNumberOrdinal', $request->request->get('inventory_request_paper_detail_grid')['filter']['masterOrderHeader:codeNumberOrdinal']);
//                $add['sort']($qb, 'm', 'codeNumberOrdinal', $request->request->get('inventory_request_paper_detail_grid')['sort']['masterOrderHeader:codeNumberOrdinal']);
//            }
//            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['masterOrderHeader:codeNumberMonth']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['masterOrderHeader:codeNumberMonth'])) {
//                $add['filter']($qb, 'm', 'codeNumberMonth', $request->request->get('inventory_request_paper_detail_grid')['filter']['masterOrderHeader:codeNumberMonth']);
//                $add['sort']($qb, 'm', 'codeNumberMonth', $request->request->get('inventory_request_paper_detail_grid')['sort']['masterOrderHeader:codeNumberMonth']);
//            }
//            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['masterOrderHeader:codeNumberYear']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['masterOrderHeader:codeNumberYear'])) {
//                $add['filter']($qb, 'm', 'codeNumberYear', $request->request->get('inventory_request_paper_detail_grid')['filter']['masterOrderHeader:codeNumberYear']);
//                $add['sort']($qb, 'm', 'codeNumberYear', $request->request->get('inventory_request_paper_detail_grid')['sort']['masterOrderHeader:codeNumberYear']);
//            }
//            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['customer:company']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['customer:company'])) {
//                $add['filter']($qb, 'c', 'company', $request->request->get('inventory_request_paper_detail_grid')['filter']['customer:company']);
//                $add['sort']($qb, 'c', 'company', $request->request->get('inventory_request_paper_detail_grid')['sort']['customer:company']);
//            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['paper:code']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['paper:code'])) {
                $add['filter']($qb, 'p', 'code', $request->request->get('inventory_request_paper_detail_grid')['filter']['paper:code']);
                $add['sort']($qb, 'p', 'code', $request->request->get('inventory_request_paper_detail_grid')['sort']['paper:code']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['paper:name']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['paper:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('inventory_request_paper_detail_grid')['filter']['paper:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('inventory_request_paper_detail_grid')['sort']['paper:name']);
            }
        });

        return $this->renderForm("shared/inventory_request_paper_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryRequestPaperDetails' => $inventoryRequestPaperDetails,
        ]);
    }
}
