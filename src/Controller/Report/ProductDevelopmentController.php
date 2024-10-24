<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\ProductDevelopmentGridType;
use App\Repository\Production\ProductDevelopmentRepository;
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

#[Route('/report/product_development')]
class ProductDevelopmentController extends AbstractController
{
    #[Route('/_list', name: 'app_report_product_development__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function _list(Request $request, ProductDevelopmentRepository $productDevelopmentRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(ProductDevelopmentGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $productDevelopments) = $productDevelopmentRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $productDevelopments);
        } else {
            return $this->renderForm("report/product_development/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'productDevelopments' => $productDevelopments,
            ]);
        }
    }

    #[Route('/', name: 'app_report_product_development_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function index(): Response
    {
        return $this->render("report/product_development/index.html.twig");
    }

    public function export(FormInterface $form, array $productDevelopments): Response
    {
        $htmlString = $this->renderView("report/product_development/_list_export.html.twig", [
            'form' => $form->createView(),
            'productDevelopments' => $productDevelopments,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'development produk.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
