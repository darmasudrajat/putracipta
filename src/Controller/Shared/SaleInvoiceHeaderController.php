<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\SaleInvoiceHeaderGridType;
use App\Repository\Sale\SaleInvoiceHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/sale_invoice_header')]
class SaleInvoiceHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_sale_invoice_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SaleInvoiceHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleInvoiceHeaders) = $saleInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias) use ($request) {
            $customerId = '';
            if (isset($request->request->get('sale_payment_header')['customer'])) {
                $customerId = $request->request->get('sale_payment_header')['customer'];
            }
            $qb->andWhere("IDENTITY({$alias}.customer) = :customerId");
            $qb->setParameter('customerId', $customerId);
            $qb->andWhere("{$alias}.remainingPayment > 0.00");
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/sale_invoice_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleInvoiceHeaders' => $saleInvoiceHeaders,
        ]);
    }
}
