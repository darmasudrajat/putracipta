<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderCuttingHeader;
use App\Form\Production\WorkOrderCuttingHeaderType;
use App\Grid\Production\WorkOrderCuttingHeaderGridType;
use App\Repository\Production\WorkOrderCuttingHeaderRepository;
use App\Service\Production\WorkOrderCuttingHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/work_order_cutting_header')]
class WorkOrderCuttingHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_work_order_cutting_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderCuttingHeaderRepository $workOrderCuttingHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderCuttingHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $workOrderCuttingHeaders) = $workOrderCuttingHeaderRepository->fetchData($criteria);

        return $this->renderForm("production/work_order_cutting_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderCuttingHeaders' => $workOrderCuttingHeaders,
        ]);
    }

    #[Route('/', name: 'app_production_work_order_cutting_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/work_order_cutting_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_work_order_cutting_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderCuttingHeaderFormService $workOrderCuttingHeaderFormService, $_format = 'html'): Response
    {
        $workOrderCuttingHeader = new WorkOrderCuttingHeader();
        $workOrderCuttingHeaderFormService->initialize($workOrderCuttingHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderCuttingHeaderType::class, $workOrderCuttingHeader);
        $form->handleRequest($request);
        $workOrderCuttingHeaderFormService->finalize($workOrderCuttingHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderCuttingHeaderFormService->save($workOrderCuttingHeader);

            return $this->redirectToRoute('app_production_work_order_cutting_header_show', ['id' => $workOrderCuttingHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_cutting_header/new.{$_format}.twig", [
            'workOrderCuttingHeader' => $workOrderCuttingHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_work_order_cutting_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderCuttingHeader $workOrderCuttingHeader): Response
    {
        return $this->render('production/work_order_cutting_header/show.html.twig', [
            'workOrderCuttingHeader' => $workOrderCuttingHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_work_order_cutting_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderCuttingHeader $workOrderCuttingHeader, WorkOrderCuttingHeaderFormService $workOrderCuttingHeaderFormService, $_format = 'html'): Response
    {
        $workOrderCuttingHeaderFormService->initialize($workOrderCuttingHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderCuttingHeaderType::class, $workOrderCuttingHeader);
        $form->handleRequest($request);
        $workOrderCuttingHeaderFormService->finalize($workOrderCuttingHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderCuttingHeaderFormService->save($workOrderCuttingHeader);

            return $this->redirectToRoute('app_production_work_order_cutting_header_show', ['id' => $workOrderCuttingHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_cutting_header/edit.{$_format}.twig", [
            'workOrderCuttingHeader' => $workOrderCuttingHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_work_order_cutting_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderCuttingHeader $workOrderCuttingHeader, WorkOrderCuttingHeaderRepository $workOrderCuttingHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderCuttingHeader->getId(), $request->request->get('_token'))) {
            $workOrderCuttingHeaderRepository->remove($workOrderCuttingHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_work_order_cutting_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
