<?php

namespace App\Controller\Sale;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleInvoiceDetail;
use App\Entity\Sale\SaleInvoiceHeader;
use App\Form\Sale\SaleInvoiceHeaderType;
use App\Grid\Sale\SaleInvoiceHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Sale\DeliveryDetailRepository;
use App\Repository\Sale\SaleInvoiceHeaderRepository;
use App\Service\Sale\SaleInvoiceHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sale/sale_invoice_header')]
class SaleInvoiceHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_sale_sale_invoice_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function _list(Request $request, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(SaleInvoiceHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleInvoiceHeaders) = $saleInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('sale_invoice_header_grid')['filter']['customer:company']) && isset($request->request->get('sale_invoice_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('sale_invoice_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('sale_invoice_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("sale/sale_invoice_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleInvoiceHeaders' => $saleInvoiceHeaders,
        ]);
    }

    #[Route('/', name: 'app_sale_sale_invoice_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function index(): Response
    {
        return $this->render("sale/sale_invoice_header/index.html.twig");
    }

    #[Route('/_list_outstanding_delivery_header', name: 'app_sale_sale_invoice_header__list_outstanding_delivery_header', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function _listOutstandingDeliveryHeader(Request $request, DeliveryDetailRepository $deliveryDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $deliveryDetails) = $deliveryDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) {
            $sub = $new(SaleInvoiceDetail::class, 's');
            $sub->andWhere("IDENTITY(s.deliveryDetail) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("sale/sale_invoice_header/_list_outstanding_delivery_header.html.twig", [
            'form' => $form,
            'count' => $count,
            'deliveryDetails' => $deliveryDetails,
        ]);
    }

    #[Route('/index_outstanding_delivery_header', name: 'app_sale_sale_invoice_header_index_outstanding_delivery_header', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function indexOutstandingDeliveryHeader(): Response
    {
        return $this->render("sale/sale_invoice_header/index_outstanding_delivery_header.html.twig");
    }

    #[Route('/_head', name: 'app_sale_sale_invoice_header__head', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function _head(Request $request, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $saleInvoiceHeaders) = $saleInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("sale/sale_invoice_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleInvoiceHeaders' => $saleInvoiceHeaders,
        ]);
    }

    #[Route('/head', name: 'app_sale_sale_invoice_header_head', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function head(): Response
    {
        return $this->render("sale/sale_invoice_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_sale_sale_invoice_header_read', methods: ['POST'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function read(Request $request, SaleInvoiceHeader $saleInvoiceHeader, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $saleInvoiceHeader->getId(), $request->request->get('_token'))) {
            $saleInvoiceHeader->setIsRead(true);
            $saleInvoiceHeaderRepository->add($saleInvoiceHeader, true);
        }

        return $this->redirectToRoute('app_sale_sale_invoice_header_show', ['id' => $saleInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_sale_sale_invoice_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_INVOICE_ADD')]
    public function new(Request $request, SaleInvoiceHeaderFormService $saleInvoiceHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleInvoiceHeader = new SaleInvoiceHeader();
        $saleInvoiceHeaderFormService->initialize($saleInvoiceHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleInvoiceHeaderType::class, $saleInvoiceHeader);
        $form->handleRequest($request);
        $saleInvoiceHeaderFormService->finalize($saleInvoiceHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderFormService->save($saleInvoiceHeader);

            return $this->redirectToRoute('app_sale_sale_invoice_header_show', ['id' => $saleInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_invoice_header/new.{$_format}.twig", [
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sale_sale_invoice_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function show(SaleInvoiceHeader $saleInvoiceHeader): Response
    {
        return $this->render('sale/sale_invoice_header/show.html.twig', [
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_sale_sale_invoice_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_INVOICE_EDIT')]
    public function edit(Request $request, SaleInvoiceHeader $saleInvoiceHeader, SaleInvoiceHeaderFormService $saleInvoiceHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleInvoiceHeaderFormService->initialize($saleInvoiceHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleInvoiceHeaderType::class, $saleInvoiceHeader);
        $form->handleRequest($request);
        $saleInvoiceHeaderFormService->finalize($saleInvoiceHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderFormService->save($saleInvoiceHeader);

            return $this->redirectToRoute('app_sale_sale_invoice_header_show', ['id' => $saleInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_invoice_header/edit.{$_format}.twig", [
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_sale_sale_invoice_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SALE_INVOICE_EDIT')]
    public function delete(Request $request, SaleInvoiceHeader $saleInvoiceHeader, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $saleInvoiceHeader->getId(), $request->request->get('_token'))) {
            $saleInvoiceHeaderRepository->remove($saleInvoiceHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_sale_sale_invoice_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/complete', name: 'app_sale_sale_invoice_header_complete', methods: ['POST'])]
    #[IsGranted('ROLE_SALE_INVOICE_EDIT')]
    public function complete(Request $request, SaleInvoiceHeader $saleInvoiceHeader, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('complete' . $saleInvoiceHeader->getId(), $request->request->get('_token')) && IdempotentUtility::check($request)) {
            $saleInvoiceHeaderRepository = $entityManager->getRepository(SaleInvoiceHeader::class);

            $saleInvoiceHeader->setRemainingPayment('0.00');
            $saleInvoiceHeader->setTransactionStatus(SaleInvoiceHeader::TRANSACTION_STATUS_FULL_PAYMENT);
            $saleInvoiceHeaderRepository->add($saleInvoiceHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was completed successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to complete the record.'));
        }

        return $this->redirectToRoute('app_sale_sale_invoice_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_sale_sale_invoice_header_memo', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_INVOICE_ADD') or is_granted('ROLE_SALE_INVOICE_EDIT') or is_granted('ROLE_SALE_INVOICE_VIEW')")]
    public function memo(SaleInvoiceHeader $saleInvoiceHeader, LiteralConfigRepository $literalConfigRepository): Response
    {
        $fileName = 'sale_invoice.pdf';
        $htmlView = $this->render('sale/sale_invoice_header/memo.html.twig', [
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'ifscCode' => $literalConfigRepository->findLiteralValue('ifscCode'),
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
}
