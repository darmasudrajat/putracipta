<?php

namespace App\Controller\Sale;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SalePaymentHeader;
use App\Form\Sale\SalePaymentHeaderType;
use App\Grid\Sale\SalePaymentHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Sale\SalePaymentHeaderRepository;
use App\Service\Sale\SalePaymentHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sale/sale_payment_header')]
class SalePaymentHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_sale_sale_payment_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SALE_PAYMENT_ADD') or is_granted('ROLE_SALE_PAYMENT_EDIT') or is_granted('ROLE_SALE_PAYMENT_VIEW')")]
    public function _list(Request $request, SalePaymentHeaderRepository $salePaymentHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(SalePaymentHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $salePaymentHeaders) = $salePaymentHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('sale_payment_header_grid')['filter']['customer:company']) && isset($request->request->get('sale_payment_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('sale_payment_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('sale_payment_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("sale/sale_payment_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'salePaymentHeaders' => $salePaymentHeaders,
        ]);
    }

    #[Route('/', name: 'app_sale_sale_payment_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_PAYMENT_ADD') or is_granted('ROLE_SALE_PAYMENT_EDIT') or is_granted('ROLE_SALE_PAYMENT_VIEW')")]
    public function index(): Response
    {
        return $this->render("sale/sale_payment_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_sale_sale_payment_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_PAYMENT_ADD')]
    public function new(Request $request, SalePaymentHeaderFormService $salePaymentHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $salePaymentHeader = new SalePaymentHeader();
        $salePaymentHeaderFormService->initialize($salePaymentHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SalePaymentHeaderType::class, $salePaymentHeader);
        $form->handleRequest($request);
        $salePaymentHeaderFormService->finalize($salePaymentHeader, ['serviceTaxPercentage' => $literalConfigRepository->findLiteralValue('serviceTaxPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $salePaymentHeaderFormService->save($salePaymentHeader);

            return $this->redirectToRoute('app_sale_sale_payment_header_show', ['id' => $salePaymentHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_payment_header/new.{$_format}.twig", [
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sale_sale_payment_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_PAYMENT_ADD') or is_granted('ROLE_SALE_PAYMENT_EDIT') or is_granted('ROLE_SALE_PAYMENT_VIEW')")]
    public function show(SalePaymentHeader $salePaymentHeader): Response
    {
        return $this->render('sale/sale_payment_header/show.html.twig', [
            'salePaymentHeader' => $salePaymentHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_sale_sale_payment_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_PAYMENT_EDIT')]
    public function edit(Request $request, SalePaymentHeader $salePaymentHeader, SalePaymentHeaderFormService $salePaymentHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $salePaymentHeaderFormService->initialize($salePaymentHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SalePaymentHeaderType::class, $salePaymentHeader);
        $form->handleRequest($request);
        $salePaymentHeaderFormService->finalize($salePaymentHeader, ['serviceTaxPercentage' => $literalConfigRepository->findLiteralValue('serviceTaxPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $salePaymentHeaderFormService->save($salePaymentHeader);

            return $this->redirectToRoute('app_sale_sale_payment_header_show', ['id' => $salePaymentHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_payment_header/edit.{$_format}.twig", [
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_sale_sale_payment_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SALE_PAYMENT_EDIT')]
    public function delete(Request $request, SalePaymentHeader $salePaymentHeader, SalePaymentHeaderRepository $salePaymentHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $salePaymentHeader->getId(), $request->request->get('_token'))) {
            $salePaymentHeaderRepository->remove($salePaymentHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_sale_sale_payment_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
