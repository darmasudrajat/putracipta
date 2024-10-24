<?php

namespace App\Controller\Sale;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleReturnHeader;
use App\Form\Sale\SaleReturnHeaderType;
use App\Grid\Sale\SaleReturnHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Sale\SaleReturnHeaderRepository;
use App\Service\Sale\SaleReturnHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sale/sale_return_header')]
class SaleReturnHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_sale_sale_return_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SALE_RETURN_ADD') or is_granted('ROLE_SALE_RETURN_EDIT') or is_granted('ROLE_SALE_RETURN_VIEW')")]
    public function _list(Request $request, SaleReturnHeaderRepository $saleReturnHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(SaleReturnHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleReturnHeaders) = $saleReturnHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('sale_return_header_grid')['filter']['customer:company']) && isset($request->request->get('sale_return_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('sale_return_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('sale_return_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("sale/sale_return_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleReturnHeaders' => $saleReturnHeaders,
        ]);
    }

    #[Route('/', name: 'app_sale_sale_return_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_RETURN_ADD') or is_granted('ROLE_SALE_RETURN_EDIT') or is_granted('ROLE_SALE_RETURN_VIEW')")]
    public function index(): Response
    {
        return $this->render("sale/sale_return_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_sale_sale_return_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_RETURN_ADD')]
    public function new(Request $request, SaleReturnHeaderFormService $saleReturnHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleReturnHeader = new SaleReturnHeader();
        $saleReturnHeaderFormService->initialize($saleReturnHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleReturnHeaderType::class, $saleReturnHeader);
        $form->handleRequest($request);
        $saleReturnHeaderFormService->finalize($saleReturnHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $saleReturnHeaderFormService->save($saleReturnHeader);

            return $this->redirectToRoute('app_sale_sale_return_header_show', ['id' => $saleReturnHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_return_header/new.{$_format}.twig", [
            'saleReturnHeader' => $saleReturnHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sale_sale_return_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_SALE_RETURN_ADD') or is_granted('ROLE_SALE_RETURN_EDIT') or is_granted('ROLE_SALE_RETURN_VIEW')")]
    public function show(SaleReturnHeader $saleReturnHeader): Response
    {
        return $this->render('sale/sale_return_header/show.html.twig', [
            'saleReturnHeader' => $saleReturnHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_sale_sale_return_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_RETURN_EDIT')]
    public function edit(Request $request, SaleReturnHeader $saleReturnHeader, SaleReturnHeaderFormService $saleReturnHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleReturnHeaderFormService->initialize($saleReturnHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleReturnHeaderType::class, $saleReturnHeader);
        $form->handleRequest($request);
        $saleReturnHeaderFormService->finalize($saleReturnHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $saleReturnHeaderFormService->save($saleReturnHeader);

            return $this->redirectToRoute('app_sale_sale_return_header_show', ['id' => $saleReturnHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/sale_return_header/edit.{$_format}.twig", [
            'saleReturnHeader' => $saleReturnHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_sale_sale_return_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SALE_RETURN_EDIT')]
    public function delete(Request $request, SaleReturnHeader $saleReturnHeader, SaleReturnHeaderRepository $saleReturnHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $saleReturnHeader->getId(), $request->request->get('_token'))) {
            $saleReturnHeaderRepository->remove($saleReturnHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_sale_sale_return_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
