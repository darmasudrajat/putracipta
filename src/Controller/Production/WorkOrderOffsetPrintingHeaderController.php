<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderOffsetPrintingHeader;
use App\Form\Production\WorkOrderOffsetPrintingHeaderType;
use App\Grid\Production\WorkOrderOffsetPrintingHeaderGridType;
use App\Repository\Production\WorkOrderOffsetPrintingHeaderRepository;
use App\Service\Production\WorkOrderOffsetPrintingHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/work_order_offset_printing_header')]
class WorkOrderOffsetPrintingHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_work_order_offset_printing_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderOffsetPrintingHeaderRepository $workOrderOffsetPrintingHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderOffsetPrintingHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $workOrderOffsetPrintingHeaders) = $workOrderOffsetPrintingHeaderRepository->fetchData($criteria);

        return $this->renderForm("production/work_order_offset_printing_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderOffsetPrintingHeaders' => $workOrderOffsetPrintingHeaders,
        ]);
    }

    #[Route('/', name: 'app_production_work_order_offset_printing_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/work_order_offset_printing_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_work_order_offset_printing_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderOffsetPrintingHeaderFormService $workOrderOffsetPrintingHeaderFormService, $_format = 'html'): Response
    {
        $workOrderOffsetPrintingHeader = new WorkOrderOffsetPrintingHeader();
        $workOrderOffsetPrintingHeaderFormService->initialize($workOrderOffsetPrintingHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderOffsetPrintingHeaderType::class, $workOrderOffsetPrintingHeader);
        $form->handleRequest($request);
        $workOrderOffsetPrintingHeaderFormService->finalize($workOrderOffsetPrintingHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderOffsetPrintingHeaderFormService->save($workOrderOffsetPrintingHeader);

            return $this->redirectToRoute('app_production_work_order_offset_printing_header_show', ['id' => $workOrderOffsetPrintingHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_offset_printing_header/new.{$_format}.twig", [
            'workOrderOffsetPrintingHeader' => $workOrderOffsetPrintingHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_work_order_offset_printing_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader): Response
    {
        return $this->render('production/work_order_offset_printing_header/show.html.twig', [
            'workOrderOffsetPrintingHeader' => $workOrderOffsetPrintingHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_work_order_offset_printing_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader, WorkOrderOffsetPrintingHeaderFormService $workOrderOffsetPrintingHeaderFormService, $_format = 'html'): Response
    {
        $workOrderOffsetPrintingHeaderFormService->initialize($workOrderOffsetPrintingHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderOffsetPrintingHeaderType::class, $workOrderOffsetPrintingHeader);
        $form->handleRequest($request);
        $workOrderOffsetPrintingHeaderFormService->finalize($workOrderOffsetPrintingHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderOffsetPrintingHeaderFormService->save($workOrderOffsetPrintingHeader);

            return $this->redirectToRoute('app_production_work_order_offset_printing_header_show', ['id' => $workOrderOffsetPrintingHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_offset_printing_header/edit.{$_format}.twig", [
            'workOrderOffsetPrintingHeader' => $workOrderOffsetPrintingHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_work_order_offset_printing_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader, WorkOrderOffsetPrintingHeaderRepository $workOrderOffsetPrintingHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderOffsetPrintingHeader->getId(), $request->request->get('_token'))) {
            $workOrderOffsetPrintingHeaderRepository->remove($workOrderOffsetPrintingHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_work_order_offset_printing_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
