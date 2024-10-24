<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Sale\SaleReturnHeader;
use App\Grid\Shared\DeliveryHeaderGridType;
use App\Repository\Sale\DeliveryHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/delivery_header')]
class DeliveryHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_delivery_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DeliveryHeaderRepository $deliveryHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(DeliveryHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $deliveryHeaders) = $deliveryHeaderRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $sub = $new(SaleReturnHeader::class, 'p');
            $sub->andWhere("IDENTITY(p.deliveryHeader) = {$alias}.id");
            $qb->leftJoin("{$alias}.saleReturnHeaders", 'r');
            $qb->andWhere($qb->expr()->orX('r.isCanceled = true', $qb->expr()->not($qb->expr()->exists($sub->getDQL()))));
            $qb->andWhere("{$alias}.isCanceled = false");
            
            if (isset($request->request->get('delivery_header_grid')['filter']['customer:company']) && isset($request->request->get('delivery_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('delivery_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('delivery_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("shared/delivery_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'deliveryHeaders' => $deliveryHeaders,
        ]);
    }
}
