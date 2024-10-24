<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Production\MasterOrderPrototypeDetail;
use App\Grid\Shared\ProductPrototypeDetailGridType;
use App\Repository\Production\ProductPrototypeDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/product_prototype_detail')]
class ProductPrototypeDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_product_prototype_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ProductPrototypeDetailRepository $productPrototypeDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ProductPrototypeDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $productPrototypeDetails) = $productPrototypeDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            
            $qb->innerJoin("{$alias}.product", 'p');
            if (isset($request->request->get('product_prototype_detail_grid')['filter']['product:code']) && isset($request->request->get('product_prototype_detail_grid')['sort']['product:code'])) {
                $add['filter']($qb, 'p', 'code', $request->request->get('product_prototype_detail_grid')['filter']['product:code']);
                $add['sort']($qb, 'p', 'code', $request->request->get('product_prototype_detail_grid')['sort']['product:code']);
            }
            
            if (isset($request->request->get('product_prototype_detail_grid')['filter']['product:name']) && isset($request->request->get('product_prototype_detail_grid')['sort']['product:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('product_prototype_detail_grid')['filter']['product:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('product_prototype_detail_grid')['sort']['product:name']);
            }
            
            if (isset($request->request->get('product_prototype_detail_grid')['filter']['product:measurement']) && isset($request->request->get('product_prototype_detail_grid')['sort']['product:measurement'])) {
                $add['filter']($qb, 'p', 'length', $request->request->get('product_prototype_detail_grid')['filter']['product:measurement']);
                $add['sort']($qb, 'p', 'length', $request->request->get('product_prototype_detail_grid')['sort']['product:measurement']);
            }
            
            $sub = $new(MasterOrderPrototypeDetail::class, 'm');
            $sub->andWhere("IDENTITY(m.productPrototypeDetail) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/product_prototype_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'productPrototypeDetails' => $productPrototypeDetails,
        ]);
    }
}
