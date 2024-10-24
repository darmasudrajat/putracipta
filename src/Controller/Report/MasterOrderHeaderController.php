<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\MasterOrderHeaderGridType;
use App\Repository\Production\MasterOrderHeaderRepository;
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

#[Route('/report/master_order_header')]
class MasterOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_master_order_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function _list(Request $request, MasterOrderHeaderRepository $masterOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(MasterOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $masterOrderHeaders) = $masterOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('master_order_header_grid')['filter']['supplier:company']) && isset($request->request->get('master_order_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('master_order_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('master_order_header_grid')['sort']['supplier:company']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $masterOrderHeaders);
        } else {
            return $this->renderForm("report/master_order_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'masterOrderHeaders' => $masterOrderHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_master_order_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function index(): Response
    {
        return $this->render("report/master_order_header/index.html.twig");
    }

    public function export(FormInterface $form, array $masterOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/master_order_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'masterOrderHeaders' => $masterOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'master_order.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
