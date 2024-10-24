<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\InventoryRequestHeaderGridType;
use App\Repository\Stock\InventoryRequestHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/inventory_request_header')]
class InventoryRequestHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_inventory_request_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryRequestHeaderRepository $inventoryRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(InventoryRequestHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryRequestHeaders) = $inventoryRequestHeaderRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.totalQuantityRemaining > 0");
        });

        return $this->renderForm("shared/inventory_request_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryRequestHeaders' => $inventoryRequestHeaders,
        ]);
    }
}
