<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Report\InventoryReleasePaperDetailGridType;
use App\Repository\Stock\InventoryReleasePaperDetailRepository;
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

#[Route('/report/inventory_release_paper_detail')]
class InventoryReleasePaperDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_release_paper_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function _list(Request $request, InventoryReleasePaperDetailRepository $inventoryReleasePaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(InventoryReleasePaperDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryReleasePaperDetails) = $inventoryReleasePaperDetailRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.inventoryReleaseHeader", 'h');
            $qb->innerJoin("{$alias}.paper", 'm');
            
            if (isset($request->request->get('inventory_release_paper_detail_grid')['filter']['inventoryReleaseHeader:warehouse']) && isset($request->request->get('inventory_release_paper_detail_grid')['sort']['inventoryReleaseHeader:warehouse'])) {
                $add['filter']($qb, 'h', 'warehouse', $request->request->get('inventory_release_paper_detail_grid')['filter']['inventoryReleaseHeader:warehouse']);
                $add['sort']($qb, 'h', 'warehouse', $request->request->get('inventory_release_paper_detail_grid')['sort']['inventoryReleaseHeader:warehouse']);
            }
            if (isset($request->request->get('inventory_release_paper_detail_grid')['filter']['inventoryReleaseHeader:note']) && isset($request->request->get('inventory_release_paper_detail_grid')['sort']['inventoryReleaseHeader:note'])) {
                $add['filter']($qb, 'h', 'note', $request->request->get('inventory_release_paper_detail_grid')['filter']['inventoryReleaseHeader:note']);
                $add['sort']($qb, 'h', 'note', $request->request->get('inventory_release_paper_detail_grid')['sort']['inventoryReleaseHeader:note']);
            }
            if (isset($request->request->get('inventory_release_paper_detail_grid')['filter']['inventoryReleaseHeader:releaseMode']) && isset($request->request->get('inventory_release_paper_detail_grid')['sort']['inventoryReleaseHeader:releaseMode'])) {
                $add['filter']($qb, 'h', 'releaseMode', $request->request->get('inventory_release_paper_detail_grid')['filter']['inventoryReleaseHeader:releaseMode']);
                $add['sort']($qb, 'h', 'releaseMode', $request->request->get('inventory_release_paper_detail_grid')['sort']['inventoryReleaseHeader:releaseMode']);
            }
            if (isset($request->request->get('inventory_release_paper_detail_grid')['filter']['paper:code']) && isset($request->request->get('inventory_release_paper_detail_grid')['sort']['paper:code'])) {
                $add['filter']($qb, 'm', 'code', $request->request->get('inventory_release_paper_detail_grid')['filter']['paper:code']);
                $add['sort']($qb, 'm', 'code', $request->request->get('inventory_release_paper_detail_grid')['sort']['paper:code']);
            }
            if (isset($request->request->get('inventory_release_paper_detail_grid')['filter']['paper:name']) && isset($request->request->get('inventory_release_paper_detail_grid')['sort']['paper:name'])) {
                $add['filter']($qb, 'm', 'name', $request->request->get('inventory_release_paper_detail_grid')['filter']['paper:name']);
                $add['sort']($qb, 'm', 'name', $request->request->get('inventory_release_paper_detail_grid')['sort']['paper:name']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $inventoryReleasePaperDetails);
        } else {
            return $this->renderForm("report/inventory_release_paper_detail/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'inventoryReleasePaperDetails' => $inventoryReleasePaperDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_inventory_release_paper_detail_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_release_paper_detail/index.html.twig");
    }

    public function export(FormInterface $form, array $inventoryReleasePaperDetails): Response
    {
        $htmlString = $this->renderView("report/inventory_release_paper_detail/_list_export.html.twig", [
            'form' => $form->createView(),
            'inventoryReleasePaperDetails' => $inventoryReleasePaperDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'pengeluaran paper detail.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
