<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\DiecutKnifeGridType;
use App\Repository\Master\DiecutKnifeRepository;
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

#[Route('/report/diecut_knife')]
class DiecutKnifeController extends AbstractController
{
    #[Route('/_list', name: 'app_report_diecut_knife__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function _list(Request $request, DiecutKnifeRepository $diecutKnifeRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'date' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(DiecutKnifeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $diecutKnifes) = $diecutKnifeRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $diecutKnifes);
        } else {
            return $this->renderForm("report/diecut_knife/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'diecutKnifes' => $diecutKnifes,
            ]);
        }
    }

    #[Route('/', name: 'app_report_diecut_knife_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function index(): Response
    {
        return $this->render("report/diecut_knife/index.html.twig");
    }

    public function export(FormInterface $form, array $diecutKnifes): Response
    {
        $htmlString = $this->renderView("report/diecut_knife/_list_export.html.twig", [
            'form' => $form->createView(),
            'diecutKnifes' => $diecutKnifes,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'pisau diecut.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
