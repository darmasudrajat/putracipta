<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\DeliveryHeaderGridType;
use App\Repository\Sale\DeliveryHeaderRepository;
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

#[Route('/report/delivery_header')]
class DeliveryHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_delivery_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function _list(Request $request, DeliveryHeaderRepository $deliveryHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(DeliveryHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $deliveryHeaders) = $deliveryHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('delivery_header_grid')['filter']['customer:company']) && isset($request->request->get('delivery_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('delivery_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('delivery_header_grid')['sort']['customer:company']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $deliveryHeaders);
        } else {
            return $this->renderForm("report/delivery_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'deliveryHeaders' => $deliveryHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_delivery_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function index(): Response
    {
        return $this->render("report/delivery_header/index.html.twig");
    }

    public function export(FormInterface $form, array $deliveryHeaders): Response
    {
        $htmlString = $this->renderView("report/delivery_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'deliveryHeaders' => $deliveryHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'delivery.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
