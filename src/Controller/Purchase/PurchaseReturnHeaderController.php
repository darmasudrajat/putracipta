<?php

namespace App\Controller\Purchase;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseReturnHeader;
use App\Form\Purchase\PurchaseReturnHeaderType;
use App\Grid\Purchase\PurchaseReturnHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Purchase\PurchaseReturnHeaderRepository;
use App\Service\Purchase\PurchaseReturnHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase/purchase_return_header')]
class PurchaseReturnHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_purchase_purchase_return_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PURCHASE_RETURN_ADD') or is_granted('ROLE_PURCHASE_RETURN_EDIT') or is_granted('ROLE_PURCHASE_RETURN_VIEW')")]
    public function _list(Request $request, PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseReturnHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseReturnHeaders) = $purchaseReturnHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('purchase_return_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_return_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_return_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_return_header_grid')['sort']['supplier:company']);
            }
//            if (isset($request->request->get('purchase_return_header_grid')['filter']['warehouse:name']) && isset($request->request->get('purchase_return_header_grid')['sort']['warehouse:name'])) {
//                $qb->innerJoin("{$alias}.warehouse", 'w');
//                $add['filter']($qb, 'w', 'name', $request->request->get('purchase_return_header_grid')['filter']['warehouse:name']);
//                $add['sort']($qb, 'w', 'name', $request->request->get('purchase_return_header_grid')['sort']['warehouse:name']);
//            }
        });

        return $this->renderForm("purchase/purchase_return_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseReturnHeaders' => $purchaseReturnHeaders,
        ]);
    }

    #[Route('/', name: 'app_purchase_purchase_return_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_RETURN_ADD') or is_granted('ROLE_PURCHASE_RETURN_EDIT') or is_granted('ROLE_PURCHASE_RETURN_VIEW')")]
    public function index(): Response
    {
        return $this->render("purchase/purchase_return_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_purchase_purchase_return_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_RETURN_ADD')]
    public function new(Request $request, PurchaseReturnHeaderFormService $purchaseReturnHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseReturnHeader = new PurchaseReturnHeader();
        $purchaseReturnHeaderFormService->initialize($purchaseReturnHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseReturnHeaderType::class, $purchaseReturnHeader);
        $form->handleRequest($request);
        $purchaseReturnHeaderFormService->finalize($purchaseReturnHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseReturnHeaderFormService->save($purchaseReturnHeader);

            return $this->redirectToRoute('app_purchase_purchase_return_header_show', ['id' => $purchaseReturnHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_return_header/new.{$_format}.twig", [
            'purchaseReturnHeader' => $purchaseReturnHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_purchase_return_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_PURCHASE_RETURN_ADD') or is_granted('ROLE_PURCHASE_RETURN_EDIT') or is_granted('ROLE_PURCHASE_RETURN_VIEW')")]
    public function show(PurchaseReturnHeader $purchaseReturnHeader): Response
    {
        return $this->render('purchase/purchase_return_header/show.html.twig', [
            'purchaseReturnHeader' => $purchaseReturnHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_purchase_purchase_return_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_RETURN_EDIT')]
    public function edit(Request $request, PurchaseReturnHeader $purchaseReturnHeader, PurchaseReturnHeaderFormService $purchaseReturnHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseReturnHeaderFormService->initialize($purchaseReturnHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseReturnHeaderType::class, $purchaseReturnHeader);
        $form->handleRequest($request);
        $purchaseReturnHeaderFormService->finalize($purchaseReturnHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseReturnHeaderFormService->save($purchaseReturnHeader);

            return $this->redirectToRoute('app_purchase_purchase_return_header_show', ['id' => $purchaseReturnHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_return_header/edit.{$_format}.twig", [
            'purchaseReturnHeader' => $purchaseReturnHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_purchase_purchase_return_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_PURCHASE_RETURN_EDIT')]
    public function delete(Request $request, PurchaseReturnHeader $purchaseReturnHeader, PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseReturnHeader->getId(), $request->request->get('_token'))) {
            $purchaseReturnHeaderRepository->remove($purchaseReturnHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_return_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
