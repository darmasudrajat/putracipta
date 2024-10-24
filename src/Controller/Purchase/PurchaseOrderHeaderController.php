<?php

namespace App\Controller\Purchase;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Form\Purchase\PurchaseOrderHeaderType;
use App\Grid\Purchase\PurchaseOrderHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Purchase\PurchaseOrderHeaderRepository;
use App\Repository\Purchase\PurchaseRequestDetailRepository;
use App\Service\Purchase\PurchaseOrderHeaderFormService;
use App\Util\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase/purchase_order_header')]
class PurchaseOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_purchase_purchase_order_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _list(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('purchase_order_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_order_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_order_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_order_header_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("purchase/purchase_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
        ]);
    }

    #[Route('/', name: 'app_purchase_purchase_order_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function index(): Response
    {
        return $this->render("purchase/purchase_order_header/index.html.twig");
    }
    
    #[Route('/_list_outstanding_purchase_request', name: 'app_purchase_purchase_order_header__list_outstanding_purchase_request', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _listOutstandingPurchaseRequest(Request $request, PurchaseRequestDetailRepository $purchaseRequestDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseRequestDetails) = $purchaseRequestDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $sub = $new(PurchaseOrderDetail::class, 'p');
            $sub->andWhere("IDENTITY(p.purchaseRequestDetail) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->join("{$alias}.purchaseRequestHeader", 'h');
            $qb->andWhere("h.transactionStatus = 'Approve'");
        });

        return $this->renderForm("purchase/purchase_order_header/_list_outstanding_purchase_request.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestDetails' => $purchaseRequestDetails,
        ]);
    }

    #[Route('/index_outstanding_purchase_request', name: 'app_purchase_purchase_order_header_index_outstanding_purchase_request', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function indexOutstandingPurchaseRequest(): Response
    {
        return $this->render("purchase/purchase_order_header/index_outstanding_purchase_request.html.twig");
    }

    #[Route('/_head', name: 'app_purchase_purchase_order_header__head', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _head(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("purchase/purchase_order_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
        ]);
    }

    #[Route('/head', name: 'app_purchase_purchase_order_header_head', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function head(): Response
    {
        return $this->render("purchase/purchase_order_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_purchase_purchase_order_header_read', methods: ['POST'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_APPROVAL')")]
    public function read(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setIsRead(true);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);
        }

        return $this->redirectToRoute('app_purchase_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_purchase_purchase_order_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_MATERIAL_ADD')]
    public function new(Request $request, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderFormService->save($purchaseOrderHeader);

            return $this->redirectToRoute('app_purchase_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_order_header/new.{$_format}.twig", [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_purchase_order_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function show(PurchaseOrderHeader $purchaseOrderHeader): Response
    {
        return $this->render('purchase/purchase_order_header/show.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
        ]);
    }

    #[Route('/{source_id}/new_repeat.{_format}', name: 'app_purchase_purchase_order_header_new_repeat', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_MATERIAL_ADD')]
    public function newRepeat(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $sourcePurchaseOrderHeader = $purchaseOrderHeaderRepository->find($request->attributes->getInt('source_id'));
        $purchaseOrderHeader = $purchaseOrderHeaderFormService->copyFrom($sourcePurchaseOrderHeader);
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderFormService->save($purchaseOrderHeader);

            return $this->redirectToRoute('app_purchase_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_order_header/new_repeat.{$_format}.twig", [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_purchase_purchase_order_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT')]
    public function edit(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderFormService->save($purchaseOrderHeader);

            return $this->redirectToRoute('app_purchase_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_order_header/edit.{$_format}.twig", [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_purchase_purchase_order_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT')]
    public function delete(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository): Response
    {
        $success = false;
        if (IdempotentUtility::check($request) && $this->isCsrfTokenValid('delete' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['cancelTransaction' => true, 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
            $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader, ['cancelTransaction' => true, 'vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);
            $purchaseOrderHeaderFormService->save($purchaseOrderHeader);
            $success = true;
        }

        if ($success) {
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/approve', name: 'app_purchase_purchase_order_header_approve', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function approve(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('approve' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setApprovedTransactionDateTime(new \DateTime());
            $purchaseOrderHeader->setApprovedTransactionUser($this->getUser());
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_APPROVE);
            $purchaseOrderHeader->setIsRead(true);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was approved successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/reject', name: 'app_purchase_purchase_order_header_reject', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function reject(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('reject' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setRejectedTransactionDateTime(new \DateTime());
            $purchaseOrderHeader->setRejectedTransactionUser($this->getUser());
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_REJECT);
            $purchaseOrderHeader->setRejectNote($request->request->get('reject_note'));
            $purchaseOrderHeader->setIsRead(true);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was rejected successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/hold', name: 'app_purchase_purchase_order_header_hold', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT')]
    public function hold(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('hold' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setIsOnHold(true);
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_HOLD);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was hold successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to hold the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/release', name: 'app_purchase_purchase_order_header_release', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT')]
    public function release(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('release' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setIsOnHold(false);
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_RELEASE);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was release successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to release the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/complete', name: 'app_purchase_purchase_order_header_complete', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT')]
    public function complete(Request $request, PurchaseOrderHeader $purchaseOrderHeader, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('complete' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeaderRepository = $entityManager->getRepository(PurchaseOrderHeader::class);
            $purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
            
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_FULL_RECEIVE);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader);

            foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
                $purchaseOrderDetail->setIsTransactionClosed(true);
                $purchaseOrderDetailRepository->add($purchaseOrderDetail);
            }
            
            $entityManager->flush();
        
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was completed successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to completed the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_purchase_purchase_order_header_memo', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_ORDER_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_ORDER_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function memo(PurchaseOrderHeader $purchaseOrderHeader): Response
    {
        $fileName = 'purchase-order.pdf';
        $htmlView = $this->renderView('purchase/purchase_order_header/memo.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="signature"(.+)src=".+">/', '<img id="signature"\1src="' . $chrootDir . 'images/purchasing.jpg">', $html),
        ]);
    }
}
