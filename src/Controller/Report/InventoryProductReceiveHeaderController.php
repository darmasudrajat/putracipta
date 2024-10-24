<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\InventoryProductReceiveHeaderGridType;
use App\Repository\Stock\InventoryProductReceiveHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/inventory_product_receive_header')]
class InventoryProductReceiveHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_product_receive_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function _list(Request $request, InventoryProductReceiveHeaderRepository $inventoryProductReceiveHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(InventoryProductReceiveHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryProductReceiveHeaders) = $inventoryProductReceiveHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($criteria) {
            if (!empty($criteria->getFilter()['customer:company'][1])) {
                $qb->innerJoin("{$alias}.masterOrderHeader", 'm');
                $qb->innerJoin("m.customer", 'c');
                $add['filter']($qb, 'c', 'company', $criteria->getFilter()['customer:company']);
            }
            
        });

        return $this->renderForm("report/inventory_product_receive_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryProductReceiveHeaders' => $inventoryProductReceiveHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_inventory_product_receive_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_product_receive_header/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_inventory_product_receive_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(InventoryProductReceiveHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(InventoryProductReceiveHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/inventory_product_receive_header/export.xml.twig', array(
//            'grid' => $grid->createView(),
//        ));
//        $excelObject = $excelXmlReader->load($xml);
//        $writer = $excel->createWriter($excelObject, 'Excel5');
//        $response = $excel->createStreamedResponse($writer);
//
//        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'report.xls');
//        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
//        $response->headers->set('Pragma', 'public');
//        $response->headers->set('Cache-Control', 'maxage=1');
//        $response->headers->set('Content-Disposition', $dispositionHeader);
//
//        return $response;
//    }
}
