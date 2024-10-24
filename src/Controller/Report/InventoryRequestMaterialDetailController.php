<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Report\InventoryRequestMaterialDetailGridType;
use App\Repository\Stock\InventoryRequestMaterialDetailRepository;
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

#[Route('/report/inventory_request_material_detail')]
class InventoryRequestMaterialDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_request_material_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function _list(Request $request, InventoryRequestMaterialDetailRepository $inventoryRequestMaterialDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(InventoryRequestMaterialDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryRequestMaterialDetails) = $inventoryRequestMaterialDetailRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.inventoryRequestHeader", 'h');
            $qb->innerJoin("{$alias}.material", 'm');
            
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestHeader:warehouse']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestHeader:warehouse'])) {
                $add['filter']($qb, 'h', 'warehouse', $request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestHeader:warehouse']);
                $add['sort']($qb, 'h', 'warehouse', $request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestHeader:warehouse']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestHeader:note']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestHeader:note'])) {
                $add['filter']($qb, 'h', 'note', $request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestHeader:note']);
                $add['sort']($qb, 'h', 'note', $request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestHeader:note']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestHeader:requestMode']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestHeader:requestMode'])) {
                $add['filter']($qb, 'h', 'requestMode', $request->request->get('inventory_request_material_detail_grid')['filter']['inventoryRequestHeader:requestMode']);
                $add['sort']($qb, 'h', 'requestMode', $request->request->get('inventory_request_material_detail_grid')['sort']['inventoryRequestHeader:requestMode']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['material:code']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['material:code'])) {
                $add['filter']($qb, 'm', 'code', $request->request->get('inventory_request_material_detail_grid')['filter']['material:code']);
                $add['sort']($qb, 'm', 'code', $request->request->get('inventory_request_material_detail_grid')['sort']['material:code']);
            }
            if (isset($request->request->get('inventory_request_material_detail_grid')['filter']['material:name']) && isset($request->request->get('inventory_request_material_detail_grid')['sort']['material:name'])) {
                $add['filter']($qb, 'm', 'name', $request->request->get('inventory_request_material_detail_grid')['filter']['material:name']);
                $add['sort']($qb, 'm', 'name', $request->request->get('inventory_request_material_detail_grid')['sort']['material:name']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $inventoryRequestMaterialDetails);
        } else {
            return $this->renderForm("report/inventory_request_material_detail/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'inventoryRequestMaterialDetails' => $inventoryRequestMaterialDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_inventory_request_material_detail_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_request_material_detail/index.html.twig");
    }

    public function export(FormInterface $form, array $inventoryRequestMaterialDetails): Response
    {
        $htmlString = $this->renderView("report/inventory_request_material_detail/_list_export.html.twig", [
            'form' => $form->createView(),
            'inventoryRequestMaterialDetails' => $inventoryRequestMaterialDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'permintaan produksi material.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
