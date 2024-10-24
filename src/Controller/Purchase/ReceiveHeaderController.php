<?php

namespace App\Controller\Purchase;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\ReceiveHeader;
use App\Form\Purchase\ReceiveHeaderType;
use App\Grid\Purchase\PurchaseOrderDetailGridType;
use App\Grid\Purchase\PurchaseOrderPaperDetailGridType;
use App\Grid\Purchase\ReceiveHeaderGridType;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Purchase\ReceiveHeaderRepository;
use App\Service\Purchase\ReceiveHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase/receive_header')]
class ReceiveHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_purchase_receive_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_RECEIVE_ADD') or is_granted('ROLE_RECEIVE_EDIT') or is_granted('ROLE_RECEIVE_VIEW')")]
    public function _list(Request $request, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(ReceiveHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $receiveHeaders) = $receiveHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('receive_header_grid')['filter']['supplier:company']) && isset($request->request->get('receive_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('receive_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('receive_header_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("purchase/receive_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'receiveHeaders' => $receiveHeaders,
        ]);
    }

    #[Route('/', name: 'app_purchase_receive_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_RECEIVE_ADD') or is_granted('ROLE_RECEIVE_EDIT') or is_granted('ROLE_RECEIVE_VIEW')")]
    public function index(): Response
    {
        return $this->render("purchase/receive_header/index.html.twig");
    }

    #[Route('/_list_outstanding_purchase_order', name: 'app_purchase_receive_header__list_outstanding_purchase_order', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_RECEIVE_ADD') or is_granted('ROLE_RECEIVE_EDIT') or is_granted('ROLE_RECEIVE_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _listOutstandingPurchaseOrder(Request $request, PurchaseOrderDetailRepository $purchaseOrderDetailRepository, PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository): Response
    {
        $materialCriteria = new DataCriteria();
        $formMaterial = $this->createForm(PurchaseOrderDetailGridType::class, $materialCriteria);
        $formMaterial->handleRequest($request);

        list($countMaterial, $purchaseOrderDetails) = $purchaseOrderDetailRepository->fetchData($materialCriteria, function($qb, $alias, $add) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.remainingReceive > 0");
            $qb->join("{$alias}.purchaseOrderHeader", 'h');
            $qb->join("{$alias}.material", 'm');
            $qb->andWhere("h.transactionStatus IN ('approve', 'partial_receive')");
            if (isset($request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:codeNumberOrdinal']) && isset($request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'h', 'codeNumberOrdinal', $request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:codeNumberOrdinal']);
                $add['sort']($qb, 'h', 'codeNumberOrdinal', $request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:codeNumberMonth']) && isset($request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'h', 'codeNumberMonth', $request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:codeNumberMonth']);
                $add['sort']($qb, 'h', 'codeNumberMonth', $request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:codeNumberYear']) && isset($request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:codeNumberYear'])) {
                $add['filter']($qb, 'h', 'codeNumberYear', $request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:codeNumberYear']);
                $add['sort']($qb, 'h', 'codeNumberYear', $request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:codeNumberYear']);
            }
            if (isset($request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:transactionDate']) && isset($request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:transactionDate'])) {
                $add['filter']($qb, 'h', 'transactionDate', $request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:transactionDate']);
                $add['sort']($qb, 'h', 'transactionDate', $request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:transactionDate']);
            }
            if (isset($request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:transactionStatus']) && isset($request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:transactionStatus'])) {
                $add['filter']($qb, 'h', 'company', $request->request->get('purchase_order_detail_grid')['filter']['purchaseOrderHeader:transactionStatus']);
                $add['sort']($qb, 'h', 'company', $request->request->get('purchase_order_detail_grid')['sort']['purchaseOrderHeader:transactionStatus']);
            }
            if (isset($request->request->get('purchase_order_detail_grid')['filter']['material:code']) && isset($request->request->get('purchase_order_detail_grid')['sort']['material:code'])) {
                $add['filter']($qb, 'm', 'code', $request->request->get('purchase_order_detail_grid')['filter']['material:code']);
                $add['sort']($qb, 'm', 'code', $request->request->get('purchase_order_detail_grid')['sort']['material:code']);
            }
            if (isset($request->request->get('purchase_order_detail_grid')['filter']['material:name']) && isset($request->request->get('purchase_order_detail_grid')['sort']['material:name'])) {
                $add['filter']($qb, 'm', 'name', $request->request->get('purchase_order_detail_grid')['filter']['material:name']);
                $add['sort']($qb, 'm', 'name', $request->request->get('purchase_order_detail_grid')['sort']['material:name']);
            }
            if (isset($request->request->get('purchase_order_detail_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_order_detail_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("h.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_order_detail_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_order_detail_grid')['sort']['supplier:company']);
            }
        });

        $paperCriteria = new DataCriteria();
        $formPaper = $this->createForm(PurchaseOrderPaperDetailGridType::class, $paperCriteria);
        $formPaper->handleRequest($request);
        list($countPaper, $purchaseOrderPaperDetails) = $purchaseOrderPaperDetailRepository->fetchData($paperCriteria, function($qb, $alias, $add) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.remainingReceive > 0");
            $qb->join("{$alias}.purchaseOrderPaperHeader", 'h');
            $qb->join("{$alias}.paper", 'p');
            $qb->andWhere("h.transactionStatus IN ('partial_receive')");
            if (isset($request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:codeNumberOrdinal']) && isset($request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'h', 'codeNumberOrdinal', $request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:codeNumberOrdinal']);
                $add['sort']($qb, 'h', 'codeNumberOrdinal', $request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:codeNumberMonth']) && isset($request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'h', 'codeNumberMonth', $request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:codeNumberMonth']);
                $add['sort']($qb, 'h', 'codeNumberMonth', $request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:codeNumberYear']) && isset($request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:codeNumberYear'])) {
                $add['filter']($qb, 'h', 'codeNumberYear', $request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:codeNumberYear']);
                $add['sort']($qb, 'h', 'codeNumberYear', $request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:codeNumberYear']);
            }
            if (isset($request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:transactionDate']) && isset($request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:transactionDate'])) {
                $add['filter']($qb, 'h', 'transactionDate', $request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:transactionDate']);
                $add['sort']($qb, 'h', 'transactionDate', $request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:transactionDate']);
            }
            if (isset($request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:transactionStatus']) && isset($request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:transactionStatus'])) {
                $add['filter']($qb, 'h', 'company', $request->request->get('purchase_order_paper_detail_grid')['filter']['purchaseOrderPaperHeader:transactionStatus']);
                $add['sort']($qb, 'h', 'company', $request->request->get('purchase_order_paper_detail_grid')['sort']['purchaseOrderPaperHeader:transactionStatus']);
            }
            if (isset($request->request->get('purchase_order_paper_detail_grid')['filter']['paper:code']) && isset($request->request->get('purchase_order_paper_detail_grid')['sort']['paper:code'])) {
                $add['filter']($qb, 'p', 'code', $request->request->get('purchase_order_paper_detail_grid')['filter']['paper:code']);
                $add['sort']($qb, 'p', 'code', $request->request->get('purchase_order_paper_detail_grid')['sort']['paper:code']);
            }
            if (isset($request->request->get('purchase_order_paper_detail_grid')['filter']['paper:name']) && isset($request->request->get('purchase_order_paper_detail_grid')['sort']['paper:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('purchase_order_paper_detail_grid')['filter']['paper:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('purchase_order_paper_detail_grid')['sort']['paper:name']);
            }
            if (isset($request->request->get('purchase_order_paper_detail_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_order_paper_detail_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("h.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_order_paper_detail_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_order_paper_detail_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("purchase/receive_header/_list_outstanding_purchase_order.html.twig", [
            'formMaterial' => $formMaterial,
            'formPaper' => $formPaper,
            'countMaterial' => $countMaterial,
            'countPaper' => $countPaper,
            'purchaseOrderDetails' => $purchaseOrderDetails,
            'purchaseOrderPaperDetails' => $purchaseOrderPaperDetails,
        ]);
    }

    #[Route('/index_outstanding_purchase_order', name: 'app_purchase_receive_header_index_outstanding_purchase_order', methods: ['GET'])]
    #[Security("is_granted('ROLE_RECEIVE_ADD') or is_granted('ROLE_RECEIVE_EDIT') or is_granted('ROLE_RECEIVE_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function indexOutstandingPurchaseOrder(): Response
    {
        return $this->render("purchase/receive_header/index_outstanding_purchase_order.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_purchase_receive_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RECEIVE_ADD')]
    public function new(Request $request, ReceiveHeaderFormService $receiveHeaderFormService, $_format = 'html'): Response
    {
        $receiveHeader = new ReceiveHeader();
        $receiveHeaderFormService->initialize($receiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);
        $receiveHeaderFormService->finalize($receiveHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $receiveHeaderFormService->save($receiveHeader);

            return $this->redirectToRoute('app_purchase_receive_header_show', ['id' => $receiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/receive_header/new.{$_format}.twig", [
            'receiveHeader' => $receiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_receive_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_RECEIVE_ADD') or is_granted('ROLE_RECEIVE_EDIT') or is_granted('ROLE_RECEIVE_VIEW')")]
    public function show(ReceiveHeader $receiveHeader): Response
    {
        return $this->render('purchase/receive_header/show.html.twig', [
            'receiveHeader' => $receiveHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_purchase_receive_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RECEIVE_EDIT')]
    public function edit(Request $request, ReceiveHeader $receiveHeader, ReceiveHeaderFormService $receiveHeaderFormService, $_format = 'html'): Response
    {
        $receiveHeaderFormService->initialize($receiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);
        $receiveHeaderFormService->finalize($receiveHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $receiveHeaderFormService->save($receiveHeader);

            return $this->redirectToRoute('app_purchase_receive_header_show', ['id' => $receiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/receive_header/edit.{$_format}.twig", [
            'receiveHeader' => $receiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_purchase_receive_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RECEIVE_EDIT')]
    public function delete(Request $request, ReceiveHeader $receiveHeader, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $receiveHeader->getId(), $request->request->get('_token'))) {
            $receiveHeaderRepository->remove($receiveHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_purchase_receive_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
