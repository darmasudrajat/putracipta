<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\DesignCodeGridType;
use App\Repository\Master\DesignCodeRepository;
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

#[Route('/report/design_code')]
class DesignCodeController extends AbstractController
{
    #[Route('/_list', name: 'app_report_design_code__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function _list(Request $request, DesignCodeRepository $designCodeRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'createdTransactionDateTime' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(DesignCodeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $designCodes) = $designCodeRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $designCodes);
        } else {
            return $this->renderForm("report/design_code/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'designCodes' => $designCodes,
            ]);
        }
    }

    #[Route('/', name: 'app_report_design_code_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function index(): Response
    {
        return $this->render("report/design_code/index.html.twig");
    }

    public function export(FormInterface $form, array $designCodes): Response
    {
        $htmlString = $this->renderView("report/design_code/_list_export.html.twig", [
            'form' => $form->createView(),
            'designCodes' => $designCodes,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'design code.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
