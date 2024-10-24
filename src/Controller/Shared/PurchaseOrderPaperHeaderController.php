<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Grid\Shared\PurchaseOrderPaperHeaderGridType;
use App\Repository\Purchase\PurchaseOrderPaperHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/purchase_order_paper_header')]
class PurchaseOrderPaperHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_purchase_order_paper_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseOrderPaperHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->andWhere("{$alias}.totalRemainingReceive > 0");
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.transactionStatus = 'Approve' OR {$alias}.transactionStatus = 'partial_receive'");
            
            if (isset($request->request->get('purchase_order_paper_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_order_paper_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_order_paper_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_order_paper_header_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("shared/purchase_order_paper_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
        ]);
    }
}
