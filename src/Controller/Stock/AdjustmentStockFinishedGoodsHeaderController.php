<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\AdjustmentStockHeader;
use App\Form\Stock\AdjustmentStockHeaderType;
use App\Grid\Stock\AdjustmentStockHeaderGridType;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use App\Service\Stock\AdjustmentStockHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/adjustment_stock_finished_goods_header')]
class AdjustmentStockFinishedGoodsHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_adjustment_stock_finished_goods_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADJUSTMENT_ADD') or is_granted('ROLE_ADJUSTMENT_EDIT') or is_granted('ROLE_ADJUSTMENT_VIEW')")]
    public function _list(Request $request, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(AdjustmentStockHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $adjustmentStockHeaders) = $adjustmentStockHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.adjustmentMode IN ('product')");
        });

        return $this->renderForm("stock/adjustment_stock_finished_goods_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'adjustmentStockHeaders' => $adjustmentStockHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_adjustment_stock_finished_goods_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADJUSTMENT_ADD') or is_granted('ROLE_ADJUSTMENT_EDIT') or is_granted('ROLE_ADJUSTMENT_VIEW')")]
    public function index(): Response
    {
        return $this->render("stock/adjustment_stock_finished_goods_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_adjustment_stock_finished_goods_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADJUSTMENT_ADD')]
    public function new(Request $request, AdjustmentStockHeaderFormService $adjustmentStockHeaderFormService, $_format = 'html'): Response
    {
        $adjustmentStockHeader = new AdjustmentStockHeader();
        $adjustmentStockHeaderFormService->initialize($adjustmentStockHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser(), 'isFinishedGoods' => true]);
        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader, ['isFinishedGoods' => true]);
        $form->handleRequest($request);
        $adjustmentStockHeaderFormService->finalize($adjustmentStockHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $adjustmentStockHeaderFormService->save($adjustmentStockHeader);

            return $this->redirectToRoute('app_stock_adjustment_stock_finished_goods_header_show', ['id' => $adjustmentStockHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/adjustment_stock_finished_goods_header/new.{$_format}.twig", [
            'adjustmentStockHeader' => $adjustmentStockHeader,
            'form' => $form,
            'isFinishedGoods' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_adjustment_stock_finished_goods_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADJUSTMENT_ADD') or is_granted('ROLE_ADJUSTMENT_EDIT') or is_granted('ROLE_ADJUSTMENT_VIEW')")]
    public function show(AdjustmentStockHeader $adjustmentStockHeader): Response
    {
        return $this->render('stock/adjustment_stock_finished_goods_header/show.html.twig', [
            'adjustmentStockHeader' => $adjustmentStockHeader,
        ]);
    }

//    #[Route('/{id}/edit.{_format}', name: 'app_stock_adjustment_stock_header_edit', methods: ['GET', 'POST'])]
//    #[IsGranted('ROLE_ADJUSTMENT_EDIT')]
//    public function edit(Request $request, AdjustmentStockHeader $adjustmentStockHeader, AdjustmentStockHeaderFormService $adjustmentStockHeaderFormService, $_format = 'html'): Response
//    {
//        $adjustmentStockHeaderFormService->initialize($adjustmentStockHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
//        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader);
//        $form->handleRequest($request);
//        $adjustmentStockHeaderFormService->finalize($adjustmentStockHeader);
//
//        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
//            $adjustmentStockHeaderFormService->save($adjustmentStockHeader);
//
//            return $this->redirectToRoute('app_stock_adjustment_stock_header_show', ['id' => $adjustmentStockHeader->getId()], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm("stock/adjustment_stock_header/edit.{$_format}.twig", [
//            'adjustmentStockHeader' => $adjustmentStockHeader,
//            'form' => $form,
//        ]);
//    }

//    #[Route('/{id}/delete', name: 'app_stock_adjustment_stock_header_delete', methods: ['POST'])]
//    #[IsGranted('ROLE_ADJUSTMENT_EDIT')]
//    public function delete(Request $request, AdjustmentStockHeader $adjustmentStockHeader, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $adjustmentStockHeader->getId(), $request->request->get('_token'))) {
//            $adjustmentStockHeaderRepository->remove($adjustmentStockHeader, true);
//
//            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
//        } else {
//            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
//        }
//
//        return $this->redirectToRoute('app_stock_adjustment_stock_header_index', [], Response::HTTP_SEE_OTHER);
//    }
}
