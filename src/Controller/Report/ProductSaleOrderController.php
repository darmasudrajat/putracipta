<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;
use App\Grid\Report\ProductSaleOrderGridType;
use App\Repository\Master\ProductRepository;
use App\Repository\Sale\SaleOrderDetailRepository;
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

#[Route('/report/product_sale_order')]
class ProductSaleOrderController extends AbstractController
{
    #[Route('/_product_choice_list', name: 'app_report_product_sale_order__product_choice_list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function _productChoiceList(Request $request, ProductRepository $productRepository): Response
    {
        $productId = '';
        if (isset($request->request->get('product_sale_order_grid')['filter']['id'][1])) {
            $productId = $request->request->get('product_sale_order_grid')['filter']['id'][1];
        }
        $customerId = '';
        if (isset($request->request->get('product_sale_order_grid')['filter']['customer'][1])) {
            $customerId = $request->request->get('product_sale_order_grid')['filter']['customer'][1];
        }
        $products = $productRepository->findBy(['isInactive' => false, 'customer' => $customerId], ['name' => 'ASC']);

        return $this->render("report/product_sale_order/_product_choice_list.html.twig", [
            'products' => $products,
            'productId' => $productId,
        ]);
    }

    #[Route('/_list', name: 'app_report_product_sale_order__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function _list(Request $request, ProductRepository $productRepository, SaleOrderDetailRepository $saleOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'saleOrderHeader:orderReceiveDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(ProductSaleOrderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $products) = $productRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT d.id FROM " . SaleOrderDetail::class . " d INNER JOIN " . SaleOrderHeader::class . " h WHERE {$alias} = d.product AND h.orderReceiveDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][2]);
            $qb->addOrderBy("{$alias}.name", 'ASC');
        });
        $saleOrderDetails = $this->getSaleOrderDetails($saleOrderDetailRepository, $criteria, $products);

        if ($request->request->has('export')) {
            return $this->export($form, $products, $saleOrderDetails);
        } else {
            return $this->renderForm("report/product_sale_order/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'products' => $products,
                'saleOrderDetails' => $saleOrderDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_product_sale_order_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/product_sale_order/index.html.twig");
    }

    private function getSaleOrderDetails(SaleOrderDetailRepository $saleOrderDetailRepository, DataCriteria $criteria, array $products): array
    {
        $startDate = $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][1];
        $endDate = $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][2];
        $productSaleOrderDetails = $saleOrderDetailRepository->findProductSaleOrderDetails($products, $startDate, $endDate);
        $saleOrderDetails = [];
        foreach ($productSaleOrderDetails as $productSaleOrderDetail) {
            $saleOrderDetails[$productSaleOrderDetail->getProduct()->getId()][] = $productSaleOrderDetail;
        }

        return $saleOrderDetails;
    }

    public function export(FormInterface $form, array $products, array $saleOrderDetails): Response
    {
        $htmlString = $this->renderView("report/product_sale_order/_list_export.html.twig", [
            'form' => $form->createView(),
            'products' => $products,
            'saleOrderDetails' => $saleOrderDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_order_per_product.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
