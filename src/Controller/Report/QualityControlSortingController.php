<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\QualityControlSortingGridType;
use App\Repository\Production\QualityControlSortingHeaderRepository;
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

#[Route('/report/quality_control_sorting')]
class QualityControlSortingController extends AbstractController
{
    #[Route('/_list', name: 'app_report_quality_control_sorting__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function _list(Request $request, QualityControlSortingHeaderRepository $qualityControlSortingHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(QualityControlSortingGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $qualityControlSortings) = $qualityControlSortingHeaderRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $qualityControlSortings);
        } else {
            return $this->renderForm("report/quality_control_sorting/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'qualityControlSortings' => $qualityControlSortings,
            ]);
        }
    }

    #[Route('/', name: 'app_report_quality_control_sorting_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function index(): Response
    {
        return $this->render("report/quality_control_sorting/index.html.twig");
    }

    public function export(FormInterface $form, array $qualityControlSortings): Response
    {
        $htmlString = $this->renderView("report/quality_control_sorting/_list_export.html.twig", [
            'form' => $form->createView(),
            'qualityControlSortings' => $qualityControlSortings,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'qc sortir.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
