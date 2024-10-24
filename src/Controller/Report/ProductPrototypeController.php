<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\ProductPrototypeGridType;
use App\Repository\Production\ProductPrototypeRepository;
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

#[Route('/report/product_prototype')]
class ProductPrototypeController extends AbstractController
{
    #[Route('/_list', name: 'app_report_product_prototype__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function _list(Request $request, ProductPrototypeRepository $productPrototypeRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(ProductPrototypeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $productPrototypes) = $productPrototypeRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $productPrototypes);
        } else {
            return $this->renderForm("report/product_prototype/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'productPrototypes' => $productPrototypes,
            ]);
        }
    }

    #[Route('/', name: 'app_report_product_prototype_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function index(): Response
    {
        return $this->render("report/product_prototype/index.html.twig");
    }

    public function export(FormInterface $form, array $productPrototypes): Response
    {
        $htmlString = $this->renderView("report/product_prototype/_list_export.html.twig", [
            'form' => $form->createView(),
            'productPrototypes' => $productPrototypes,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'form produk baru.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
