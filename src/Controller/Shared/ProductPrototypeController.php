<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Production\ProductDevelopment;
use App\Grid\Shared\ProductPrototypeGridType;
use App\Repository\Production\ProductPrototypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/product_prototype')]
class ProductPrototypeController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_product_prototype__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ProductPrototypeRepository $productPrototypeRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ProductPrototypeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $productPrototypes) = $productPrototypeRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            
            $sub = $new(ProductDevelopment::class, 'p');
            $sub->andWhere("IDENTITY(p.productPrototype) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/product_prototype/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'productPrototypes' => $productPrototypes,
        ]);
    }
}
