<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Grid\Shared\PurchaseRequestPaperDetailGridType;
use App\Repository\Purchase\PurchaseRequestPaperDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/purchase_request_paper_detail')]
class PurchaseRequestPaperDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_purchase_request_paper_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseRequestPaperDetailRepository $purchaseRequestPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'inventoryRequestPaperHeader:pickupDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseRequestPaperDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseRequestPaperDetails) = $purchaseRequestPaperDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $sub = $new(PurchaseOrderPaperDetail::class, 'h');
            $sub->andWhere("IDENTITY(h.purchaseRequestPaperDetail) = {$alias}.id");
            $qb->leftJoin("{$alias}.purchaseOrderPaperDetails", 'd');
            $qb->innerJoin("{$alias}.purchaseRequestPaperHeader", 'r');
            $qb->andWhere($qb->expr()->orX('d.isCanceled = true', $qb->expr()->not($qb->expr()->exists($sub->getDQL()))));
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("r.transactionStatus = 'Approve'");
            $qb->innerJoin("{$alias}.paper", 'p');
            
            if (isset($request->request->get('purchase_request_paper_detail_grid')['filter']['paper:name']) && isset($request->request->get('purchase_request_paper_detail_grid')['sort']['paper:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('purchase_request_paper_detail_grid')['filter']['paper:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('purchase_request_paper_detail_grid')['sort']['paper:name']);
            }
            
            if (isset($request->request->get('purchase_request_paper_detail_grid')['filter']['paper:length']) && isset($request->request->get('purchase_request_paper_detail_grid')['sort']['paper:length'])) {
                $add['filter']($qb, 'p', 'length', $request->request->get('purchase_request_paper_detail_grid')['filter']['paper:length']);
                $add['sort']($qb, 'p', 'length', $request->request->get('purchase_request_paper_detail_grid')['sort']['paper:length']);
            }
            
            if (isset($request->request->get('purchase_request_paper_detail_grid')['filter']['paper:width']) && isset($request->request->get('purchase_request_paper_detail_grid')['sort']['paper:width'])) {
                $add['filter']($qb, 'p', 'width', $request->request->get('purchase_request_paper_detail_grid')['filter']['paper:width']);
                $add['sort']($qb, 'p', 'width', $request->request->get('purchase_request_paper_detail_grid')['sort']['paper:width']);
            }
            
            if (isset($request->request->get('purchase_request_paper_detail_grid')['filter']['paper:weight']) && isset($request->request->get('purchase_request_paper_detail_grid')['sort']['paper:weight'])) {
                $add['filter']($qb, 'p', 'weight', $request->request->get('purchase_request_paper_detail_grid')['filter']['paper:weight']);
                $add['sort']($qb, 'p', 'weight', $request->request->get('purchase_request_paper_detail_grid')['sort']['paper:weight']);
            }
            
            if (isset($request->request->get('purchase_request_paper_detail_grid')['filter']['unit:name']) && isset($request->request->get('purchase_request_paper_detail_grid')['sort']['unit:name'])) {
                $qb->innerJoin("{$alias}.unit", 'u');
                $add['filter']($qb, 'u', 'name', $request->request->get('purchase_request_paper_detail_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->request->get('purchase_request_paper_detail_grid')['sort']['unit:name']);
            }
        });

        return $this->renderForm("shared/purchase_request_paper_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestPaperDetails' => $purchaseRequestPaperDetails,
        ]);
    }
}
