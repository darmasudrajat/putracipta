<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Report\InventoryRequestPaperDetailGridType;
use App\Repository\Stock\InventoryRequestPaperDetailRepository;
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

#[Route('/report/inventory_request_paper_detail')]
class InventoryRequestPaperDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_request_paper_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function _list(Request $request, InventoryRequestPaperDetailRepository $inventoryRequestPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(InventoryRequestPaperDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryRequestPaperDetails) = $inventoryRequestPaperDetailRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.inventoryRequestHeader", 'h');
            $qb->innerJoin("{$alias}.paper", 'm');
            
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestHeader:warehouse']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestHeader:warehouse'])) {
                $add['filter']($qb, 'h', 'warehouse', $request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestHeader:warehouse']);
                $add['sort']($qb, 'h', 'warehouse', $request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestHeader:warehouse']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestHeader:note']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestHeader:note'])) {
                $add['filter']($qb, 'h', 'note', $request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestHeader:note']);
                $add['sort']($qb, 'h', 'note', $request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestHeader:note']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestHeader:requestMode']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestHeader:requestMode'])) {
                $add['filter']($qb, 'h', 'requestMode', $request->request->get('inventory_request_paper_detail_grid')['filter']['inventoryRequestHeader:requestMode']);
                $add['sort']($qb, 'h', 'requestMode', $request->request->get('inventory_request_paper_detail_grid')['sort']['inventoryRequestHeader:requestMode']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['paper:code']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['paper:code'])) {
                $add['filter']($qb, 'm', 'code', $request->request->get('inventory_request_paper_detail_grid')['filter']['paper:code']);
                $add['sort']($qb, 'm', 'code', $request->request->get('inventory_request_paper_detail_grid')['sort']['paper:code']);
            }
            if (isset($request->request->get('inventory_request_paper_detail_grid')['filter']['paper:name']) && isset($request->request->get('inventory_request_paper_detail_grid')['sort']['paper:name'])) {
                $add['filter']($qb, 'm', 'name', $request->request->get('inventory_request_paper_detail_grid')['filter']['paper:name']);
                $add['sort']($qb, 'm', 'name', $request->request->get('inventory_request_paper_detail_grid')['sort']['paper:name']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $inventoryRequestPaperDetails);
        } else {
            return $this->renderForm("report/inventory_request_paper_detail/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'inventoryRequestPaperDetails' => $inventoryRequestPaperDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_inventory_request_paper_detail_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_request_paper_detail/index.html.twig");
    }

    public function export(FormInterface $form, array $inventoryRequestPaperDetails): Response
    {
        $htmlString = $this->renderView("report/inventory_request_paper_detail/_list_export.html.twig", [
            'form' => $form->createView(),
            'inventoryRequestPaperDetails' => $inventoryRequestPaperDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'permintaan produksi paper.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
