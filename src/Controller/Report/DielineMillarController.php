<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\DielineMillarGridType;
use App\Repository\Master\DielineMillarRepository;
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

#[Route('/report/dieline_millar')]
class DielineMillarController extends AbstractController
{
    #[Route('/_list', name: 'app_report_dieline_millar__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function _list(Request $request, DielineMillarRepository $dielineMillarRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'date' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(DielineMillarGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $dielineMillars) = $dielineMillarRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $dielineMillars);
        } else {
            return $this->renderForm("report/dieline_millar/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'dielineMillars' => $dielineMillars,
            ]);
        }
    }

    #[Route('/', name: 'app_report_dieline_millar_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function index(): Response
    {
        return $this->render("report/dieline_millar/index.html.twig");
    }

    public function export(FormInterface $form, array $dielineMillars): Response
    {
        $htmlString = $this->renderView("report/dieline_millar/_list_export.html.twig", [
            'form' => $form->createView(),
            'dielineMillars' => $dielineMillars,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'millar.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
