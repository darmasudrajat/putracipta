<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\QualityControlSortingHeader;
use App\Form\Production\QualityControlSortingHeaderType;
use App\Grid\Production\QualityControlSortingHeaderGridType;
use App\Repository\Production\QualityControlSortingHeaderRepository;
use App\Service\Production\QualityControlSortingHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/quality_control_sorting_header')]
class QualityControlSortingHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_quality_control_sorting_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function _list(Request $request, QualityControlSortingHeaderRepository $qualityControlSortingHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(QualityControlSortingHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $qualityControlSortingHeaders) = $qualityControlSortingHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.masterOrderHeader", 'm');
            if (isset($request->request->get('quality_control_sorting_header_grid')['filter']['customer:company']) && isset($request->request->get('quality_control_sorting_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('quality_control_sorting_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('quality_control_sorting_header_grid')['sort']['customer:company']);
            }
            if (isset($request->request->get('quality_control_sorting_header_grid')['filter']['masterOrderHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'm', 'codeNumberOrdinal', $request->request->get('quality_control_sorting_header_grid')['filter']['masterOrderHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('quality_control_sorting_header_grid')['filter']['masterOrderHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'm', 'codeNumberMonth', $request->request->get('quality_control_sorting_header_grid')['filter']['masterOrderHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('quality_control_sorting_header_grid')['filter']['masterOrderHeader:codeNumberYear'])) {
                $add['filter']($qb, 'm', 'codeNumberYear', $request->request->get('quality_control_sorting_header_grid')['filter']['masterOrderHeader:codeNumberYear']);
            }
        });

        return $this->renderForm("production/quality_control_sorting_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'qualityControlSortingHeaders' => $qualityControlSortingHeaders,
        ]);
    }

    #[Route('/', name: 'app_production_quality_control_sorting_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function index(): Response
    {
        return $this->render("production/quality_control_sorting_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_quality_control_sorting_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_NEW_PRODUCT_ADD')]
    public function new(Request $request, QualityControlSortingHeaderFormService $qualityControlSortingHeaderFormService, $_format = 'html'): Response
    {
        $qualityControlSortingHeader = new QualityControlSortingHeader();
        $qualityControlSortingHeaderFormService->initialize($qualityControlSortingHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(QualityControlSortingHeaderType::class, $qualityControlSortingHeader);
        $form->handleRequest($request);
        $qualityControlSortingHeaderFormService->finalize($qualityControlSortingHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $qualityControlSortingHeaderFormService->save($qualityControlSortingHeader);

            return $this->redirectToRoute('app_production_quality_control_sorting_header_show', ['id' => $qualityControlSortingHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/quality_control_sorting_header/new.{$_format}.twig", [
            'qualityControlSortingHeader' => $qualityControlSortingHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_quality_control_sorting_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function show(QualityControlSortingHeader $qualityControlSortingHeader): Response
    {
        return $this->render('production/quality_control_sorting_header/show.html.twig', [
            'qualityControlSortingHeader' => $qualityControlSortingHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_quality_control_sorting_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_NEW_PRODUCT_EDIT')]
    public function edit(Request $request, QualityControlSortingHeader $qualityControlSortingHeader, QualityControlSortingHeaderFormService $qualityControlSortingHeaderFormService, $_format = 'html'): Response
    {
        $qualityControlSortingHeaderFormService->initialize($qualityControlSortingHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(QualityControlSortingHeaderType::class, $qualityControlSortingHeader);
        $form->handleRequest($request);
        $qualityControlSortingHeaderFormService->finalize($qualityControlSortingHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $qualityControlSortingHeaderFormService->save($qualityControlSortingHeader);

            return $this->redirectToRoute('app_production_quality_control_sorting_header_show', ['id' => $qualityControlSortingHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/quality_control_sorting_header/edit.{$_format}.twig", [
            'qualityControlSortingHeader' => $qualityControlSortingHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_quality_control_sorting_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_NEW_PRODUCT_EDIT')]
    public function delete(Request $request, QualityControlSortingHeader $qualityControlSortingHeader, QualityControlSortingHeaderRepository $qualityControlSortingHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $qualityControlSortingHeader->getId(), $request->request->get('_token'))) {
            $qualityControlSortingHeaderRepository->remove($qualityControlSortingHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_quality_control_sorting_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
