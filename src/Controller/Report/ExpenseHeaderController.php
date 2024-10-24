<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\ExpenseHeaderGridType;
use App\Repository\Accounting\ExpenseHeaderRepository;
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

#[Route('/report/expense_header')]
class ExpenseHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_expense_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, ExpenseHeaderRepository $expenseHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(ExpenseHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $expenseHeaders) = $expenseHeaderRepository->fetchData($criteria);

        if ($request->request->has('export')) {
            return $this->export($form, $expenseHeaders);
        } else {
        return $this->renderForm("report/expense_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'expenseHeaders' => $expenseHeaders,
        ]);
        }
    }

    #[Route('/', name: 'app_report_expense_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/expense_header/index.html.twig");
    }

    public function export(FormInterface $form, array $expenseHeaders): Response
    {
        $htmlString = $this->renderView("report/expense_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'expenseHeaders' => $expenseHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'pengeluaran kas bank.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
