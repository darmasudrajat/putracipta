<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\StockTransferHeader;
use App\Form\Stock\StockTransferHeaderType;
use App\Grid\Stock\StockTransferHeaderGridType;
use App\Repository\Stock\StockTransferHeaderRepository;
use App\Service\Stock\StockTransferHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/stock_transfer_header')]
class StockTransferHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_stock_transfer_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_TRANSFER_ADD') or is_granted('ROLE_TRANSFER_EDIT') or is_granted('ROLE_TRANSFER_VIEW')")]
    public function _list(Request $request, StockTransferHeaderRepository $stockTransferHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(StockTransferHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $stockTransferHeaders) = $stockTransferHeaderRepository->fetchData($criteria);

        return $this->renderForm("stock/stock_transfer_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'stockTransferHeaders' => $stockTransferHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_stock_transfer_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_TRANSFER_ADD') or is_granted('ROLE_TRANSFER_EDIT') or is_granted('ROLE_TRANSFER_VIEW')")]
    public function index(): Response
    {
        return $this->render("stock/stock_transfer_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_stock_transfer_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TRANSFER_ADD')]
    public function new(Request $request, StockTransferHeaderFormService $stockTransferHeaderFormService, $_format = 'html'): Response
    {
        $stockTransferHeader = new StockTransferHeader();
        $stockTransferHeaderFormService->initialize($stockTransferHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(StockTransferHeaderType::class, $stockTransferHeader);
        $form->handleRequest($request);
        $stockTransferHeaderFormService->finalize($stockTransferHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $stockTransferHeaderFormService->save($stockTransferHeader);

            return $this->redirectToRoute('app_stock_stock_transfer_header_show', ['id' => $stockTransferHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/stock_transfer_header/new.{$_format}.twig", [
            'stockTransferHeader' => $stockTransferHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_stock_transfer_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_TRANSFER_ADD') or is_granted('ROLE_TRANSFER_EDIT') or is_granted('ROLE_TRANSFER_VIEW')")]
    public function show(StockTransferHeader $stockTransferHeader): Response
    {
        return $this->render('stock/stock_transfer_header/show.html.twig', [
            'stockTransferHeader' => $stockTransferHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_stock_stock_transfer_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TRANSFER_EDIT')]
    public function edit(Request $request, StockTransferHeader $stockTransferHeader, StockTransferHeaderFormService $stockTransferHeaderFormService, $_format = 'html'): Response
    {
        $stockTransferHeaderFormService->initialize($stockTransferHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(StockTransferHeaderType::class, $stockTransferHeader);
        $form->handleRequest($request);
        $stockTransferHeaderFormService->finalize($stockTransferHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $stockTransferHeaderFormService->save($stockTransferHeader);

            return $this->redirectToRoute('app_stock_stock_transfer_header_show', ['id' => $stockTransferHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/stock_transfer_header/edit.{$_format}.twig", [
            'stockTransferHeader' => $stockTransferHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_stock_transfer_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_TRANSFER_EDIT')]
    public function delete(Request $request, StockTransferHeader $stockTransferHeader, StockTransferHeaderRepository $stockTransferHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stockTransferHeader->getId(), $request->request->get('_token'))) {
            $stockTransferHeaderRepository->remove($stockTransferHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_stock_transfer_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
