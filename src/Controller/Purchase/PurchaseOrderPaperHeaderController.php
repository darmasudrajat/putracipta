<?php

namespace App\Controller\Purchase;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Form\Purchase\PurchaseOrderPaperHeaderType;
use App\Grid\Purchase\PurchaseOrderPaperHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Purchase\PurchaseOrderPaperHeaderRepository;
use App\Repository\Purchase\PurchaseRequestPaperDetailRepository;
use App\Service\Purchase\PurchaseOrderPaperHeaderFormService;
use App\Util\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase/purchase_order_paper_header')]
class PurchaseOrderPaperHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_purchase_purchase_order_paper_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _list(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseOrderPaperHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('purchase_order_paper_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_order_paper_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_order_paper_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_order_paper_header_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("purchase/purchase_order_paper_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
        ]);
    }

    #[Route('/', name: 'app_purchase_purchase_order_paper_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function index(): Response
    {
        return $this->render("purchase/purchase_order_paper_header/index.html.twig");
    }

    #[Route('/_list_outstanding_purchase_request_paper', name: 'app_purchase_purchase_order_paper_header__list_outstanding_purchase_request_paper', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _listOutstandingPurchaseRequestPaper(Request $request, PurchaseRequestPaperDetailRepository $purchaseRequestPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseRequestPaperDetails) = $purchaseRequestPaperDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $sub = $new(PurchaseOrderPaperDetail::class, 'p');
            $sub->andWhere("IDENTITY(p.purchaseRequestPaperDetail) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->join("{$alias}.purchaseRequestPaperHeader", 'h');
            $qb->andWhere("h.transactionStatus = 'Approve'");
        });

        return $this->renderForm("purchase/purchase_order_paper_header/_list_outstanding_purchase_request_paper.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestPaperDetails' => $purchaseRequestPaperDetails,
        ]);
    }

    #[Route('/index_outstanding_purchase_request_paper', name: 'app_purchase_purchase_order_paper_header_index_outstanding_purchase_request_paper', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function indexOutstandingPurchaseRequest(): Response
    {
        return $this->render("purchase/purchase_order_paper_header/index_outstanding_purchase_request_paper.html.twig");
    }

    #[Route('/_head', name: 'app_purchase_purchase_order_paper_header__head', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _head(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("purchase/purchase_order_paper_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
        ]);
    }

    #[Route('/head', name: 'app_purchase_purchase_order_paper_header_head', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function head(): Response
    {
        return $this->render("purchase/purchase_order_paper_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_purchase_purchase_order_paper_header_read', methods: ['POST'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW')")]
    public function read(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setIsRead(true);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);
        }

        return $this->redirectToRoute('app_purchase_purchase_order_paper_header_show', ['id' => $purchaseOrderPaperHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_purchase_purchase_order_paper_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_PAPER_ADD')]
    public function new(Request $request, PurchaseOrderPaperHeaderFormService $purchaseOrderPaperHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseOrderPaperHeader = new PurchaseOrderPaperHeader();
        $purchaseOrderPaperHeaderFormService->initialize($purchaseOrderPaperHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderPaperHeaderType::class, $purchaseOrderPaperHeader);
        $form->handleRequest($request);
        $purchaseOrderPaperHeaderFormService->finalize($purchaseOrderPaperHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderPaperHeaderFormService->save($purchaseOrderPaperHeader);

            return $this->redirectToRoute('app_purchase_purchase_order_paper_header_show', ['id' => $purchaseOrderPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_order_paper_header/new.{$_format}.twig", [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_purchase_order_paper_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function show(PurchaseOrderPaperHeader $purchaseOrderPaperHeader): Response
    {
        return $this->render('purchase/purchase_order_paper_header/show.html.twig', [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
        ]);
    }

    #[Route('/{source_id}/new_repeat.{_format}', name: 'app_purchase_purchase_order_paper_header_new_repeat', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_PAPER_ADD')]
    public function newRepeat(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository, PurchaseOrderPaperHeaderFormService $purchaseOrderPaperHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $sourcePurchaseOrderPaperHeader = $purchaseOrderPaperHeaderRepository->find($request->attributes->getInt('source_id'));
        $purchaseOrderPaperHeader = $purchaseOrderPaperHeaderFormService->copyFrom($sourcePurchaseOrderPaperHeader);
        $purchaseOrderPaperHeaderFormService->initialize($purchaseOrderPaperHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderPaperHeaderType::class, $purchaseOrderPaperHeader);
        $form->handleRequest($request);
        $purchaseOrderPaperHeaderFormService->finalize($purchaseOrderPaperHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderPaperHeaderFormService->save($purchaseOrderPaperHeader);

            return $this->redirectToRoute('app_purchase_purchase_order_paper_header_show', ['id' => $purchaseOrderPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_order_paper_header/new_repeat.{$_format}.twig", [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_purchase_purchase_order_paper_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_PAPER_EDIT')]
    public function edit(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderFormService $purchaseOrderPaperHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseOrderPaperHeaderFormService->initialize($purchaseOrderPaperHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderPaperHeaderType::class, $purchaseOrderPaperHeader);
        $form->handleRequest($request);
        $purchaseOrderPaperHeaderFormService->finalize($purchaseOrderPaperHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderPaperHeaderFormService->save($purchaseOrderPaperHeader);

            return $this->redirectToRoute('app_purchase_purchase_order_paper_header_show', ['id' => $purchaseOrderPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_order_paper_header/edit.{$_format}.twig", [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_purchase_purchase_order_paper_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_PAPER_EDIT')]
    public function delete(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderFormService $purchaseOrderPaperHeaderFormService, LiteralConfigRepository $literalConfigRepository): Response
    {
        $success = false;
        if (IdempotentUtility::check($request) && $this->isCsrfTokenValid('delete' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeaderFormService->initialize($purchaseOrderPaperHeader, ['cancelTransaction' => true, 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
            $purchaseOrderPaperHeaderFormService->finalize($purchaseOrderPaperHeader, ['cancelTransaction' => true, 'vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);
            $purchaseOrderPaperHeaderFormService->save($purchaseOrderPaperHeader);
            $success = true;
        }

        if ($success) {
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/approve', name: 'app_purchase_purchase_order_paper_header_approve', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function approve(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('approve' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setApprovedTransactionDateTime(new \DateTime());
            $purchaseOrderPaperHeader->setApprovedTransactionUser($this->getUser());
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_APPROVE);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was approved successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/reject', name: 'app_purchase_purchase_order_paper_header_reject', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function reject(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('reject' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setRejectedTransactionDateTime(new \DateTime());
            $purchaseOrderPaperHeader->setRejectedTransactionUser($this->getUser());
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_REJECT);
            $purchaseOrderPaperHeader->setRejectNote($request->request->get('reject_note'));
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was rejected successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/hold', name: 'app_purchase_purchase_order_paper_header_hold', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_PAPER_EDIT')]
    public function hold(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('hold' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setIsOnHold(true);
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_HOLD);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was hold successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to hold the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/release', name: 'app_purchase_purchase_order_paper_header_release', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_PAPER_EDIT')]
    public function release(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('release' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setIsOnHold(false);
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_RELEASE);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was release successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to release the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/complete', name: 'app_purchase_purchase_order_paper_header_complete', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_PAPER_EDIT')]
    public function complete(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('complete' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeaderRepository = $entityManager->getRepository(PurchaseOrderPaperHeader::class);
            $purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
            
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_FULL_RECEIVE);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader);

            foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
                $purchaseOrderPaperDetail->setIsTransactionClosed(true);
                $purchaseOrderPaperDetailRepository->add($purchaseOrderPaperDetail);
            }
            
            $entityManager->flush();
        
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was closed successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to closed the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_purchase_purchase_order_paper_header_memo', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_PAPER_ADD') or is_granted('ROLE_PURCHASE_ORDER_PAPER_EDIT') or is_granted('ROLE_PURCHASE_ORDER_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function memo(PurchaseOrderPaperHeader $purchaseOrderPaperHeader): Response
    {
        $fileName = 'purchase-order.pdf';
        $htmlView = $this->renderView('purchase/purchase_order_paper_header/memo.html.twig', [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="signature"(.+)src=".+">/', '<img id="signature"\1src="' . $chrootDir . 'images/purchasing.jpg">', $html),
        ]);
    }
}
