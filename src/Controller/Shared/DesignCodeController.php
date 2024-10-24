<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Entity\Master\DesignCodeProductDetail;
use App\Grid\Shared\DesignCodeGridType;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Sale\SaleOrderDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/design_code')]
class DesignCodeController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_design_code__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DesignCodeRepository $designCodeRepository, SaleOrderDetailRepository $saleOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'createdTransactionDateTime' => SortDescending::class,
            'name' => SortAscending::class,
        ]);
        $form = $this->createForm(DesignCodeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $designCodes) = $designCodeRepository->fetchData($criteria, function($qb, $alias) use ($request, $saleOrderDetailRepository) {
            
            $customerId = '';
            if (isset($request->request->get('master_order_header')['customer'])) {
                $customerId = $request->request->get('master_order_header')['customer'];
            } elseif (isset($request->request->get('product_prototype')['customer'])) {
                $customerId = $request->request->get('product_prototype')['customer'];
            }

            $products = [];
            if (isset($request->request->get('master_order_header')['masterOrderProductDetails'])) {
                $products = array_map(fn($item) => $saleOrderDetailRepository->find($item['saleOrderDetail'])->getProduct(), $request->request->get('master_order_header')['masterOrderProductDetails']);
            }

            $qb->andWhere("IDENTITY({$alias}.customer) = :customerId");
            $qb->setParameter('customerId', $customerId);

            $qb->andWhere("EXISTS(SELECT dd FROM "  . DesignCodeProductDetail::class . " dd WHERE IDENTITY(dd.designCode) = {$alias}.id AND dd.product IN (:products))");
            $qb->setParameter('products', $products);

            $qb->andWhere("EXISTS(SELECT dc FROM "  . DesignCodeProcessDetail::class . " dc WHERE IDENTITY(dc.designCode) = {$alias}.id)");
            
            $qb->andWhere("{$alias}.status = 'fa'");
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/design_code/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'designCodes' => $designCodes,
        ]);
    }
}
