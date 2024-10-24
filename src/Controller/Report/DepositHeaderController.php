<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\DepositHeaderGridType;
use App\Repository\Accounting\DepositHeaderRepository;
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

#[Route('/report/deposit_header')]
class DepositHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_deposit_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, DepositHeaderRepository $depositHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(DepositHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $depositHeaders) = $depositHeaderRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $depositHeaders);
        } else {
            return $this->renderForm("report/deposit_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'depositHeaders' => $depositHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_deposit_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/deposit_header/index.html.twig");
    }

    public function export(FormInterface $form, array $depositHeaders): Response
    {
        $htmlString = $this->renderView("report/deposit_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'depositHeaders' => $depositHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'penerimaan kas bank.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
