<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Production\MasterOrderHeader;
use App\Grid\Shared\ProductDevelopmentGridType;
use App\Repository\Production\ProductDevelopmentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/product_development')]
class ProductDevelopmentController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_product_development__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ProductDevelopmentRepository $productDevelopmentRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ProductDevelopmentGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $productDevelopments) = $productDevelopmentRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            
            $sub = $new(MasterOrderHeader::class, 'm');
            $sub->andWhere("IDENTITY(m.productDevelopment) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/product_development/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'productDevelopments' => $productDevelopments,
        ]);
    }
}
