<?php

namespace App\Controller\Purchase;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseRequestDetail;
use App\Entity\Purchase\PurchaseRequestHeader;
use App\Form\Purchase\PurchaseRequestHeaderType;
use App\Grid\Purchase\PurchaseRequestHeaderGridType;
use App\Repository\Purchase\PurchaseRequestHeaderRepository;
use App\Service\Purchase\PurchaseRequestHeaderFormService;
use App\Util\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase/purchase_request_header')]
class PurchaseRequestHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_purchase_purchase_request_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _list(Request $request, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseRequestHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseRequestHeaders) = $purchaseRequestHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('purchase_request_header_grid')['sort']['warehouse:name'])) {
                $qb->innerJoin("{$alias}.warehouse", 'w');
                $add['sort']($qb, 'w', 'name', $request->request->get('purchase_request_header_grid')['sort']['warehouse:name']);
            }
        });

        return $this->renderForm("purchase/purchase_request_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestHeaders' => $purchaseRequestHeaders,
        ]);
    }

    #[Route('/', name: 'app_purchase_purchase_request_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function index(): Response
    {
        return $this->render("purchase/purchase_request_header/index.html.twig");
    }

    #[Route('/_head', name: 'app_purchase_purchase_request_header__head', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _head(Request $request, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseRequestHeaders) = $purchaseRequestHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
            $qb->andWhere("{$alias}.transactionStatus = 'draft'");
        });

        return $this->renderForm("purchase/purchase_request_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestHeaders' => $purchaseRequestHeaders,
        ]);
    }

    #[Route('/head', name: 'app_purchase_purchase_request_header_head', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function head(): Response
    {
        return $this->render("purchase/purchase_request_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_purchase_purchase_request_header_read', methods: ['POST'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function read(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeader->setIsRead(true);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);
        }

        return $this->redirectToRoute('app_purchase_purchase_request_header_show', ['id' => $purchaseRequestHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/_approval', name: 'app_purchase_purchase_request_header__approval', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function _approval(Request $request, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseRequestHeaders) = $purchaseRequestHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isViewed = false");
            $qb->andWhere("{$alias}.transactionStatus = 'Approve'");
        });

        return $this->renderForm("purchase/purchase_request_header/_approval.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestHeaders' => $purchaseRequestHeaders,
        ]);
    }

    #[Route('/approval', name: 'app_purchase_purchase_request_header_approval', methods: ['GET'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function approval(): Response
    {
        return $this->render("purchase/purchase_request_header/approval.html.twig");
    }

    #[Route('/{id}/view', name: 'app_purchase_purchase_request_header_view', methods: ['POST'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function view(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('view' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeader->setIsViewed(true);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);
        }

        return $this->redirectToRoute('app_purchase_purchase_request_header_show', ['id' => $purchaseRequestHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_purchase_purchase_request_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD')]
    public function new(Request $request, PurchaseRequestHeaderFormService $purchaseRequestHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestHeader = new PurchaseRequestHeader();
        $purchaseRequestHeaderFormService->initialize($purchaseRequestHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseRequestHeaderType::class, $purchaseRequestHeader);
        $form->handleRequest($request);
        $purchaseRequestHeaderFormService->finalize($purchaseRequestHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseRequestHeaderFormService->save($purchaseRequestHeader);

            return $this->redirectToRoute('app_purchase_purchase_request_header_show', ['id' => $purchaseRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_request_header/new.{$_format}.twig", [
            'purchaseRequestHeader' => $purchaseRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_purchase_request_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function show(PurchaseRequestHeader $purchaseRequestHeader): Response
    {
        return $this->render('purchase/purchase_request_header/show.html.twig', [
            'purchaseRequestHeader' => $purchaseRequestHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_purchase_purchase_request_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT')]
    public function edit(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderFormService $purchaseRequestHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestHeaderFormService->initialize($purchaseRequestHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseRequestHeaderType::class, $purchaseRequestHeader);
        $form->handleRequest($request);
        $purchaseRequestHeaderFormService->finalize($purchaseRequestHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseRequestHeaderFormService->save($purchaseRequestHeader);

            return $this->redirectToRoute('app_purchase_purchase_request_header_show', ['id' => $purchaseRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_request_header/edit.{$_format}.twig", [
            'purchaseRequestHeader' => $purchaseRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_purchase_purchase_request_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT')]
    public function delete(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderFormService $purchaseRequestHeaderFormService): Response
    {
        $success = false;
        if (IdempotentUtility::check($request) && $this->isCsrfTokenValid('delete' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeaderFormService->initialize($purchaseRequestHeader, ['cancelTransaction' => true, 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
            $purchaseRequestHeaderFormService->finalize($purchaseRequestHeader, ['cancelTransaction' => true]);
            $purchaseRequestHeaderFormService->save($purchaseRequestHeader);
            $success = true;
        }

        if ($success) {
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/approve', name: 'app_purchase_purchase_request_header_approve', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function approve(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('approve' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeader->setApprovedTransactionDateTime(new \DateTime());
            $purchaseRequestHeader->setApprovedTransactionUser($this->getUser());
            $purchaseRequestHeader->setTransactionStatus(PurchaseRequestHeader::TRANSACTION_STATUS_APPROVE);
            $purchaseRequestHeader->setIsRead(true);
            $purchaseRequestHeader->setIsViewed(true);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was approved successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/reject', name: 'app_purchase_purchase_request_header_reject', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function reject(Request $request, PurchaseRequestHeader $purchaseRequestHeader, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeaderRepository = $entityManager->getRepository(PurchaseRequestHeader::class);
            $purchaseRequestDetailRepository = $entityManager->getRepository(PurchaseRequestDetail::class);
            
            $purchaseRequestHeader->setRejectedTransactionDateTime(new \DateTime());
            $purchaseRequestHeader->setRejectedTransactionUser($this->getUser());
            $purchaseRequestHeader->setTransactionStatus(PurchaseRequestHeader::TRANSACTION_STATUS_REJECT);
            $purchaseRequestHeader->setRejectNote($request->request->get('reject_note'));
            $purchaseRequestHeader->setIsRead(true);
            $purchaseRequestHeader->setIsViewed(true);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);

            foreach ($purchaseRequestHeader->getPurchaseRequestDetails() as $purchaseRequestDetail) {
                $purchaseRequestDetail->setTransactionStatus(PurchaseRequestDetail::TRANSACTION_STATUS_CANCEL);
                $purchaseRequestDetailRepository->add($purchaseRequestDetail, true);
            }
            
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was rejected successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_purchase_purchase_request_header_memo', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_ADD') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_MATERIAL_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function memo(PurchaseRequestHeader $purchaseRequestHeader): Response
    {
        $fileName = 'purchase-request.pdf';
        $htmlView = $this->renderView('purchase/purchase_request_header/memo.html.twig', [
            'purchaseRequestHeader' => $purchaseRequestHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
    
    #[Route('/{id}/hold', name: 'app_purchase_purchase_request_header_hold', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT')]
    public function hold(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('hold' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeader->setIsOnHold(true);
            $purchaseRequestHeader->setTransactionStatus(PurchaseRequestHeader::TRANSACTION_STATUS_HOLD);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was hold successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to hold the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/release', name: 'app_purchase_purchase_request_header_release', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_MATERIAL_EDIT')]
    public function release(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('release' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeader->setIsOnHold(false);
            $purchaseRequestHeader->setTransactionStatus(PurchaseRequestHeader::TRANSACTION_STATUS_RELEASE);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was release successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to release the purchase.'));
        }

        return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}
