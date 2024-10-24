<?php

namespace App\Controller\Purchase;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\Purchase\PurchaseRequestPaperHeader;
use App\Form\Purchase\PurchaseRequestPaperHeaderType;
use App\Grid\Purchase\PurchaseRequestPaperHeaderGridType;
use App\Repository\Purchase\PurchaseRequestPaperHeaderRepository;
use App\Service\Purchase\PurchaseRequestPaperHeaderFormService;
use App\Util\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase/purchase_request_paper_header')]
class PurchaseRequestPaperHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_purchase_purchase_request_paper_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_PAPER_ADD') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _list(Request $request, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseRequestPaperHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseRequestPaperHeaders) = $purchaseRequestPaperHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('purchase_request_paper_header_grid')['filter']['warehouse:name']) && isset($request->request->get('purchase_request_paper_header_grid')['sort']['warehouse:name'])) {
                $qb->innerJoin("{$alias}.warehouse", 'w');
                $add['filter']($qb, 'w', 'name', $request->request->get('purchase_request_paper_header_grid')['filter']['warehouse:name']);
                $add['sort']($qb, 'w', 'name', $request->request->get('purchase_request_paper_header_grid')['sort']['warehouse:name']);
            }
        });

        return $this->renderForm("purchase/purchase_request_paper_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestPaperHeaders' => $purchaseRequestPaperHeaders,
        ]);
    }

    #[Route('/', name: 'app_purchase_purchase_request_paper_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_PAPER_ADD') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function index(): Response
    {
        return $this->render("purchase/purchase_request_paper_header/index.html.twig");
    }

    #[Route('/_head', name: 'app_purchase_purchase_request_paper_header__head', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_PAPER_ADD') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _head(Request $request, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseRequestPaperHeaders) = $purchaseRequestPaperHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
            $qb->andWhere("{$alias}.transactionStatus = 'draft'");
        });

        return $this->renderForm("purchase/purchase_request_paper_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestPaperHeaders' => $purchaseRequestPaperHeaders,
        ]);
    }

    #[Route('/head', name: 'app_purchase_purchase_request_paper_header_head', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_PAPER_ADD') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function head(): Response
    {
        return $this->render("purchase/purchase_request_paper_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_purchase_purchase_request_paper_header_read', methods: ['POST'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_PAPER_ADD') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function read(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $purchaseRequestPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestPaperHeader->setIsRead(true);
            $purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader, true);
        }

        return $this->redirectToRoute('app_purchase_purchase_request_paper_header_show', ['id' => $purchaseRequestPaperHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/_approval', name: 'app_purchase_purchase_request_paper_header__approval', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function _approval(Request $request, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseRequestPaperHeaders) = $purchaseRequestPaperHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isViewed = false");
            $qb->andWhere("{$alias}.transactionStatus = 'Approve'");
        });

        return $this->renderForm("purchase/purchase_request_paper_header/_approval.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestPaperHeaders' => $purchaseRequestPaperHeaders,
        ]);
    }

    #[Route('/approval', name: 'app_purchase_purchase_request_paper_header_approval', methods: ['GET'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function approval(): Response
    {
        return $this->render("purchase/purchase_request_paper_header/approval.html.twig");
    }

    #[Route('/{id}/view', name: 'app_purchase_purchase_request_paper_header_view', methods: ['POST'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_PAPER_ADD') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function view(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('view' . $purchaseRequestPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestPaperHeader->setIsViewed(true);
            $purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader, true);
        }

        return $this->redirectToRoute('app_purchase_purchase_request_paper_header_show', ['id' => $purchaseRequestPaperHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_purchase_purchase_request_paper_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_PAPER_ADD')]
    public function new(Request $request, PurchaseRequestPaperHeaderFormService $purchaseRequestPaperHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestPaperHeader = new PurchaseRequestPaperHeader();
        $purchaseRequestPaperHeaderFormService->initialize($purchaseRequestPaperHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseRequestPaperHeaderType::class, $purchaseRequestPaperHeader);
        $form->handleRequest($request);
        $purchaseRequestPaperHeaderFormService->finalize($purchaseRequestPaperHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseRequestPaperHeaderFormService->save($purchaseRequestPaperHeader);

            return $this->redirectToRoute('app_purchase_purchase_request_paper_header_show', ['id' => $purchaseRequestPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_request_paper_header/new.{$_format}.twig", [
            'purchaseRequestPaperHeader' => $purchaseRequestPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_purchase_request_paper_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_PAPER_ADD') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function show(PurchaseRequestPaperHeader $purchaseRequestPaperHeader): Response
    {
        return $this->render('purchase/purchase_request_paper_header/show.html.twig', [
            'purchaseRequestPaperHeader' => $purchaseRequestPaperHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_purchase_purchase_request_paper_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_PAPER_EDIT')]
    public function edit(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderFormService $purchaseRequestPaperHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestPaperHeaderFormService->initialize($purchaseRequestPaperHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseRequestPaperHeaderType::class, $purchaseRequestPaperHeader);
        $form->handleRequest($request);
        $purchaseRequestPaperHeaderFormService->finalize($purchaseRequestPaperHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseRequestPaperHeaderFormService->save($purchaseRequestPaperHeader);

            return $this->redirectToRoute('app_purchase_purchase_request_paper_header_show', ['id' => $purchaseRequestPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_request_paper_header/edit.{$_format}.twig", [
            'purchaseRequestPaperHeader' => $purchaseRequestPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_purchase_purchase_request_paper_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_PAPER_EDIT')]
    public function delete(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderFormService $purchaseRequestPaperHeaderFormService): Response
    {
        $success = false;
        if (IdempotentUtility::check($request) && $this->isCsrfTokenValid('delete' . $purchaseRequestPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestPaperHeaderFormService->initialize($purchaseRequestPaperHeader, ['cancelTransaction' => true, 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
            $purchaseRequestPaperHeaderFormService->finalize($purchaseRequestPaperHeader, ['cancelTransaction' => true]);
            $purchaseRequestPaperHeaderFormService->save($purchaseRequestPaperHeader);
            $success = true;
        }

        if ($success) {
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/approve', name: 'app_purchase_purchase_request_paper_header_approve', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function approve(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('approve' . $purchaseRequestPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestPaperHeader->setApprovedTransactionDateTime(new \DateTime());
            $purchaseRequestPaperHeader->setApprovedTransactionUser($this->getUser());
            $purchaseRequestPaperHeader->setTransactionStatus(PurchaseRequestPaperHeader::TRANSACTION_STATUS_APPROVE);
            $purchaseRequestPaperHeader->setIsRead(true);
            $purchaseRequestPaperHeader->setIsViewed(true);
            $purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was approved successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/reject', name: 'app_purchase_purchase_request_paper_header_reject', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function reject(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject' . $purchaseRequestPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestPaperHeaderRepository = $entityManager->getRepository(PurchaseRequestPaperHeader::class);
            $purchaseRequestPaperDetailRepository = $entityManager->getRepository(PurchaseRequestPaperDetail::class);
            
            $purchaseRequestPaperHeader->setRejectedTransactionDateTime(new \DateTime());
            $purchaseRequestPaperHeader->setRejectedTransactionUser($this->getUser());
            $purchaseRequestPaperHeader->setTransactionStatus(PurchaseRequestPaperHeader::TRANSACTION_STATUS_REJECT);
            $purchaseRequestPaperHeader->setRejectNote($request->request->get('reject_note'));
            $purchaseRequestPaperHeader->setIsRead(true);
            $purchaseRequestPaperHeader->setIsViewed(true);
            $purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader, true);

            foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
                $purchaseRequestPaperDetail->setTransactionStatus(PurchaseRequestPaperDetail::TRANSACTION_STATUS_CANCEL);
                $purchaseRequestPaperDetailRepository->add($purchaseRequestPaperDetail, true);
            }
            
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was rejected successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_purchase_purchase_request_paper_header_memo', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_REQUEST_PAPER_ADD') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_EDIT') or is_granted('ROLE_PURCHASE_REQUEST_PAPER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function memo(PurchaseRequestPaperHeader $purchaseRequestPaperHeader): Response
    {
        $fileName = 'purchase-request-paper.pdf';
        $htmlView = $this->renderView('purchase/purchase_request_paper_header/memo.html.twig', [
            'purchaseRequestPaperHeader' => $purchaseRequestPaperHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }

    #[Route('/{id}/hold', name: 'app_purchase_purchase_request_paper_header_hold', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_PAPER_EDIT')]
    public function hold(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('hold' . $purchaseRequestPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestPaperHeader->setIsOnHold(true);
            $purchaseRequestPaperHeader->setTransactionStatus(PurchaseRequestPaperHeader::TRANSACTION_STATUS_HOLD);
            $purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was hold successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to hold the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/release', name: 'app_purchase_purchase_request_paper_header_release', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_REQUEST_PAPER_EDIT')]
    public function release(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('release' . $purchaseRequestPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestPaperHeader->setIsOnHold(false);
            $purchaseRequestPaperHeader->setTransactionStatus(PurchaseRequestPaperHeader::TRANSACTION_STATUS_RELEASE);
            $purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The purchase was release successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to release the purchase.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_request_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
