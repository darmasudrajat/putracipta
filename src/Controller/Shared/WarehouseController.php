<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\WarehouseGridType;
use App\Repository\Master\WarehouseRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/warehouse')]
class WarehouseController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_warehouse__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WarehouseRepository $warehouseRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WarehouseGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $warehouses) = $warehouseRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/warehouse/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'warehouses' => $warehouses,
        ]);
    }
}
