<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\InventoryProductReceiveHeader;
use App\Form\Stock\InventoryProductReceiveHeaderType;
use App\Grid\Stock\InventoryProductReceiveHeaderGridType;
use App\Repository\Stock\InventoryProductReceiveHeaderRepository;
use App\Service\Stock\InventoryProductReceiveHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/inventory_product_receive_header')]
class InventoryProductReceiveHeaderController extends AbstractController
{
    #[Route('/_receive_list', name: 'app_stock_inventory_product_receive_header__receive_list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_FINISHED_GOODS_RECEIVE_ADD') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_EDIT') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_VIEW')")]
    public function _receiveList(Request $request, InventoryProductReceiveHeaderRepository $inventoryProductReceiveHeaderRepository): Response
    {
        $lastInventoryProductReceiveHeaders = $inventoryProductReceiveHeaderRepository->findBy(['masterOrderHeader' => $request->request->get('inventory_product_receive_header')['masterOrderHeader']], ['id' => 'DESC'], 5, 0);

        return $this->render("stock/inventory_product_receive_header/_receive_list.html.twig", [
            'lastInventoryProductReceiveHeaders' => $lastInventoryProductReceiveHeaders,
        ]);
    }

    #[Route('/_list', name: 'app_stock_inventory_product_receive_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_FINISHED_GOODS_RECEIVE_ADD') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_EDIT') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_VIEW')")]
    public function _list(Request $request, InventoryProductReceiveHeaderRepository $inventoryProductReceiveHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(InventoryProductReceiveHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryProductReceiveHeaders) = $inventoryProductReceiveHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.masterOrderHeader", 'm');
            $qb->innerJoin("m.customer", 's');
            if (isset($request->request->get('inventory_product_receive_header_grid')['filter']['customer:company'])) {
                $add['filter']($qb, 's', 'company', $request->request->get('inventory_product_receive_header_grid')['filter']['customer:company']);
            }
            if (isset($request->request->get('inventory_product_receive_header_grid')['filter']['masterOrderHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'm', 'codeNumberOrdinal', $request->request->get('inventory_product_receive_header_grid')['filter']['masterOrderHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('inventory_product_receive_header_grid')['filter']['masterOrderHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'm', 'codeNumberMonth', $request->request->get('inventory_product_receive_header_grid')['filter']['masterOrderHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('inventory_product_receive_header_grid')['filter']['masterOrderHeader:codeNumberYear'])) {
                $add['filter']($qb, 'm', 'codeNumberYear', $request->request->get('inventory_product_receive_header_grid')['filter']['masterOrderHeader:codeNumberYear']);
            }
        });

        return $this->renderForm("stock/inventory_product_receive_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryProductReceiveHeaders' => $inventoryProductReceiveHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_inventory_product_receive_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_FINISHED_GOODS_RECEIVE_ADD') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_EDIT') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_VIEW')")]
    public function index(): Response
    {
        return $this->render("stock/inventory_product_receive_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_inventory_product_receive_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINISHED_GOODS_RECEIVE_ADD')]
    public function new(Request $request, InventoryProductReceiveHeaderFormService $inventoryProductReceiveHeaderFormService, $_format = 'html'): Response
    {
        $inventoryProductReceiveHeader = new InventoryProductReceiveHeader();
        $inventoryProductReceiveHeaderFormService->initialize($inventoryProductReceiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryProductReceiveHeaderType::class, $inventoryProductReceiveHeader);
        $form->handleRequest($request);
        $inventoryProductReceiveHeaderFormService->finalize($inventoryProductReceiveHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryProductReceiveHeaderFormService->save($inventoryProductReceiveHeader);

            return $this->redirectToRoute('app_stock_inventory_product_receive_header_show', ['id' => $inventoryProductReceiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_product_receive_header/new.{$_format}.twig", [
            'inventoryProductReceiveHeader' => $inventoryProductReceiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_inventory_product_receive_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_FINISHED_GOODS_RECEIVE_ADD') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_EDIT') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_VIEW')")]
    public function show(InventoryProductReceiveHeader $inventoryProductReceiveHeader): Response
    {
        return $this->render('stock/inventory_product_receive_header/show.html.twig', [
            'inventoryProductReceiveHeader' => $inventoryProductReceiveHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_stock_inventory_product_receive_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINISHED_GOODS_RECEIVE_EDIT')]
    public function edit(Request $request, InventoryProductReceiveHeader $inventoryProductReceiveHeader, InventoryProductReceiveHeaderFormService $inventoryProductReceiveHeaderFormService, $_format = 'html'): Response
    {
        $inventoryProductReceiveHeaderFormService->initialize($inventoryProductReceiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryProductReceiveHeaderType::class, $inventoryProductReceiveHeader);
        $form->handleRequest($request);
        $inventoryProductReceiveHeaderFormService->finalize($inventoryProductReceiveHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryProductReceiveHeaderFormService->save($inventoryProductReceiveHeader);

            return $this->redirectToRoute('app_stock_inventory_product_receive_header_show', ['id' => $inventoryProductReceiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_product_receive_header/edit.{$_format}.twig", [
            'inventoryProductReceiveHeader' => $inventoryProductReceiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/memo_inventory_product_receive_header', name: 'app_stock_inventory_product_receive_header_memo', methods: ['GET'])]
    #[Security("is_granted('ROLE_FINISHED_GOODS_RECEIVE_ADD') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_EDIT') or is_granted('ROLE_FINISHED_GOODS_RECEIVE_VIEW')")]
    public function memo(InventoryProductReceiveHeader $inventoryProductReceiveHeader): Response
    {
        $fileName = 'inventory_product_receive.pdf';
        $htmlView = $this->renderView('stock/inventory_product_receive_header/memo.html.twig', [
            'inventoryProductReceiveHeader' => $inventoryProductReceiveHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
    
    #[Route('/{id}/delete', name: 'app_stock_inventory_product_receive_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_FINISHED_GOODS_RECEIVE_EDIT')]
    public function delete(Request $request, InventoryProductReceiveHeader $inventoryProductReceiveHeader, InventoryProductReceiveHeaderRepository $inventoryProductReceiveHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inventoryProductReceiveHeader->getId(), $request->request->get('_token'))) {
            $inventoryProductReceiveHeaderRepository->remove($inventoryProductReceiveHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_inventory_product_receive_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
