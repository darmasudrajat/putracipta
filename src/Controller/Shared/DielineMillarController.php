<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Grid\Shared\DielineMillarGridType;
use App\Repository\Master\DielineMillarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/dieline_millar')]
class DielineMillarController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_dieline_millar__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DielineMillarRepository $dielineMillarRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'name' => SortAscending::class,
        ]);
        $form = $this->createForm(DielineMillarGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $dielineMillars) = $dielineMillarRepository->fetchData($criteria, function($qb, $alias) use ($request) {
            $customerId = '';
            if (isset($request->request->get('master_order_header')['customer'])) {
                $customerId = $request->request->get('master_order_header')['customer'];
            } else if (isset($request->request->get('design_code')['customer'])) {
                $customerId = $request->request->get('design_code')['customer'];
            }
            if (!empty($customerId)) {
                $qb->andWhere("IDENTITY({$alias}.customer) = :customerId");
                $qb->setParameter('customerId', $customerId);
            }
            
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/dieline_millar/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'dielineMillars' => $dielineMillars,
        ]);
    }
}
