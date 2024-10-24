<?php

namespace App\Controller\Sale;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;
use App\Form\Sale\SaleOrderHeaderType;
use App\Grid\Sale\SaleOrderHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Sale\SaleOrderHeaderRepository;
use App\Service\Sale\SaleOrderHeaderFormService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sale/sale_order_header')]
class SaleOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_sale_sale_order_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _list(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(SaleOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderHeaders) = $saleOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('sale_order_header_grid')['filter']['customer:company']) && isset($request->request->get('sale_order_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('sale_order_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('sale_order_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("sale/sale_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);
    }

    #[Route('/', name: 'app_sale_sale_order_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function index(): Response
    {
        return $this->render("sale/sale_order_header/index.html.twig");
    }

    #[Route('/_head', name: 'app_sale_sale_order_header__head', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _head(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $saleOrderHeaders) = $saleOrderHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("sale/sale_order_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);
    }

    #[Route('/head', name: 'app_sale_sale_order_header_head', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function head(): Response
    {
        return $this->render("sale/sale_order_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_sale_sale_order_header_read', methods: ['POST'])]
    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function read(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $saleOrderHeader->getId(), $request->request->get('_token'))) {
            $saleOrderHeader->setIsRead(true);
            $saleOrderHeaderRepository->add($saleOrderHeader, true);
        }

        return $this->redirectToRoute('app_sale_sale_order_header_show', ['id' => $saleOrderHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_sale_sale_order_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_ORDER_ADD')]
    public function new(Request $request, SaleOrderHeaderFormService $saleOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleOrderHeader = new SaleOrderHeader();
        $saleOrderHeaderFormService->initialize($saleOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleOrderHeaderType::class, $saleOrderHeader);
        $form->handleRequest($request);
        $saleOrderHeaderFormService->finalize($saleOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage'), 'transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $saleOrderHeaderFormService->save($saleOrderHeader);
            $saleOrderHeaderFormService->uploadFile($saleOrderHeader, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/sale-order');

            return $this->redirectToRoute('app_sale_sale_order_header_show', ['id' => $saleOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_order_header/new.{$_format}.twig", [
            'saleOrderHeader' => $saleOrderHeader,
            'form' => $form,
            'transactionFileExists' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_sale_sale_order_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function show(SaleOrderHeader $saleOrderHeader): Response
    {
        return $this->render('sale/sale_order_header/show.html.twig', [
            'saleOrderHeader' => $saleOrderHeader,
        ]);
    }

    #[Route('/{source_id}/new_repeat.{_format}', name: 'app_sale_sale_order_header_new_repeat', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_ORDER_ADD')]
    public function newRepeat(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository, SaleOrderHeaderFormService $saleOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $sourceSaleOrderHeader = $saleOrderHeaderRepository->find($request->attributes->getInt('source_id'));
        $saleOrderHeader = $saleOrderHeaderFormService->copyFrom($sourceSaleOrderHeader);
        $saleOrderHeaderFormService->initialize($saleOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleOrderHeaderType::class, $saleOrderHeader);
        $form->handleRequest($request);
        $saleOrderHeaderFormService->finalize($saleOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage'), 'transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $saleOrderHeaderFormService->save($saleOrderHeader);
            $saleOrderHeaderFormService->uploadFile($saleOrderHeader, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/sale-order');

            return $this->redirectToRoute('app_sale_sale_order_header_show', ['id' => $saleOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_order_header/new_repeat.{$_format}.twig", [
            'saleOrderHeader' => $saleOrderHeader,
            'form' => $form,
            'transactionFileExists' => false,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_sale_sale_order_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_ORDER_EDIT')]
    public function edit(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderFormService $saleOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleOrderHeaderFormService->initialize($saleOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleOrderHeaderType::class, $saleOrderHeader);
        $form->handleRequest($request);
        $saleOrderHeaderFormService->finalize($saleOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage'), 'transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $saleOrderHeaderFormService->save($saleOrderHeader);
            $saleOrderHeaderFormService->uploadFile($saleOrderHeader, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/sale-order');

            return $this->redirectToRoute('app_sale_sale_order_header_show', ['id' => $saleOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_order_header/edit.{$_format}.twig", [
            'saleOrderHeader' => $saleOrderHeader,
            'form' => $form,
            'transactionFileExists' => file_exists($this->getParameter('kernel.project_dir') . '/public/uploads/sale-order/' . $saleOrderHeader->getId() . '.' . $saleOrderHeader->getTransactionFileExtension()),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_sale_sale_order_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SALE_ORDER_EDIT')]
    public function delete(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderFormService $saleOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository): Response
    {
        $success = false;
        if (IdempotentUtility::check($request) && $this->isCsrfTokenValid('delete' . $saleOrderHeader->getId(), $request->request->get('_token'))) {
            $form = $this->createForm(SaleOrderHeaderType::class, $saleOrderHeader);
            $form->handleRequest($request);
            $saleOrderHeaderFormService->initialize($saleOrderHeader, ['cancelTransaction' => true, 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
            $saleOrderHeaderFormService->finalize($saleOrderHeader, ['cancelTransaction' => true, 'vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage'), 'transactionFile' => $form->get('transactionFile')->getData()]);
            $saleOrderHeaderFormService->save($saleOrderHeader);
            $success = true;
        }

        if ($success) {
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_sale_sale_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/approve', name: 'app_sale_sale_order_header_approve', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function approve(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('approve' . $saleOrderHeader->getId(), $request->request->get('_token'))) {
            $saleOrderHeader->setApprovedTransactionDateTime(new \DateTime());
            $saleOrderHeader->setApprovedTransactionUser($this->getUser());
            $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_APPROVE);
            $saleOrderHeader->setIsRead(true);
            $saleOrderHeaderRepository->add($saleOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The sale was approved successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the sale.'));
        }

        return $this->redirectToRoute('app_sale_sale_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/reject', name: 'app_sale_sale_order_header_reject', methods: ['POST'])]
    #[IsGranted('ROLE_APPROVAL')]
    public function reject(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('reject' . $saleOrderHeader->getId(), $request->request->get('_token'))) {
            $saleOrderHeader->setRejectedTransactionDateTime(new \DateTime());
            $saleOrderHeader->setRejectedTransactionUser($this->getUser());
            $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_REJECT);
            $saleOrderHeader->setIsRead(true);
            $saleOrderHeaderRepository->add($saleOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The sale was rejected successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the sale.'));
        }

        return $this->redirectToRoute('app_sale_sale_order_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/hold', name: 'app_sale_sale_order_header_hold', methods: ['POST'])]
    #[IsGranted('ROLE_SALE_ORDER_EDIT')]
    public function hold(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('hold' . $saleOrderHeader->getId(), $request->request->get('_token'))) {
            $saleOrderHeader->setIsOnHold(true);
            $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_HOLD);
            $saleOrderHeaderRepository->add($saleOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The sale was hold successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to hold the sale.'));
        }

        return $this->redirectToRoute('app_sale_sale_order_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/release', name: 'app_sale_sale_order_header_release', methods: ['POST'])]
    #[IsGranted('ROLE_SALE_ORDER_EDIT')]
    public function release(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('release' . $saleOrderHeader->getId(), $request->request->get('_token'))) {
            $saleOrderHeader->setIsOnHold(false);
            $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_RELEASE);
            $saleOrderHeaderRepository->add($saleOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The sale was release successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to release the sale.'));
        }

        return $this->redirectToRoute('app_sale_sale_order_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/done', name: 'app_sale_sale_order_header_done', methods: ['POST'])]
    #[IsGranted('ROLE_SALE_ORDER_EDIT')]
    public function done(Request $request, SaleOrderHeader $saleOrderHeader, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('done' . $saleOrderHeader->getId(), $request->request->get('_token'))) {
            $saleOrderHeaderRepository = $entityManager->getRepository(SaleOrderHeader::class);
            $saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
            
            $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_FULL_DELIVERY);
            $saleOrderHeaderRepository->add($saleOrderHeader);

            foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
                $saleOrderDetail->setIsTransactionClosed(true);
                $saleOrderDetailRepository->add($saleOrderDetail);
            }
            
            $entityManager->flush();
        
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The sale was done successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to complete the sale.'));
        }

        return $this->redirectToRoute('app_sale_sale_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
