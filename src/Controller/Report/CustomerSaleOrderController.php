<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Sale\SaleOrderHeader;
use App\Grid\Report\CustomerSaleOrderGridType;
use App\Repository\Master\CustomerRepository;
use App\Repository\Sale\SaleOrderHeaderRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/customer_sale_order')]
class CustomerSaleOrderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_customer_sale_order__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function _list(Request $request, CustomerRepository $customerRepository, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'saleOrderHeader:orderReceiveDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(CustomerSaleOrderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $customers) = $customerRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT s.id FROM " . SaleOrderHeader::class . " s WHERE {$alias} = s.customer AND s.orderReceiveDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][2]);
            $qb->addOrderBy("{$alias}.id", 'ASC');
        });
        $saleOrderHeaders = $this->getSaleOrderHeaders($saleOrderHeaderRepository, $criteria, $customers);

        if ($request->request->has('export')) {
            return $this->export($form, $customers, $saleOrderHeaders);
        } else {
            return $this->renderForm("report/customer_sale_order/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'customers' => $customers,
                'saleOrderHeaders' => $saleOrderHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_customer_sale_order_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/customer_sale_order/index.html.twig");
    }

    private function getSaleOrderHeaders(SaleOrderHeaderRepository $saleOrderHeaderRepository, DataCriteria $criteria, array $customers): array
    {
        $startDate = $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][1];
        $endDate = $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][2];
        $customerSaleOrderHeaders = $saleOrderHeaderRepository->findCustomerSaleOrderHeaders($customers, $startDate, $endDate);
        $saleOrderHeaders = [];
        foreach ($customerSaleOrderHeaders as $customerSaleOrderHeader) {
            $saleOrderHeaders[$customerSaleOrderHeader->getCustomer()->getId()][] = $customerSaleOrderHeader;
        }

        return $saleOrderHeaders;
    }

    public function export(FormInterface $form, array $customers, array $saleOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/customer_sale_order/_list_export.html.twig", [
            'form' => $form->createView(),
            'customers' => $customers,
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_order_per_customer.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
