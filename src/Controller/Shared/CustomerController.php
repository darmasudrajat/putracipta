<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Grid\Shared\CustomerGridType;
use App\Repository\Master\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/customer')]
class CustomerController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_customer__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, CustomerRepository $customerRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'company' => SortAscending::class,
        ]);
        $form = $this->createForm(CustomerGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $customers) = $customerRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/customer/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'customers' => $customers,
        ]);
    }
}
