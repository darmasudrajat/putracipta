<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderColorMixing;
use App\Form\Production\WorkOrderColorMixingType;
use App\Grid\Production\WorkOrderColorMixingGridType;
use App\Repository\Production\WorkOrderColorMixingRepository;
use App\Service\Production\WorkOrderColorMixingFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/work_order_color_mixing')]
class WorkOrderColorMixingController extends AbstractController
{
    #[Route('/_list', name: 'app_production_work_order_color_mixing__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderColorMixingRepository $workOrderColorMixingRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderColorMixingGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $workOrderColorMixings) = $workOrderColorMixingRepository->fetchData($criteria);

        return $this->renderForm("production/work_order_color_mixing/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderColorMixings' => $workOrderColorMixings,
        ]);
    }

    #[Route('/', name: 'app_production_work_order_color_mixing_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/work_order_color_mixing/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_work_order_color_mixing_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderColorMixingFormService $workOrderColorMixingFormService, $_format = 'html'): Response
    {
        $workOrderColorMixing = new WorkOrderColorMixing();
        $workOrderColorMixingFormService->initialize($workOrderColorMixing, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderColorMixingType::class, $workOrderColorMixing);
        $form->handleRequest($request);
        $workOrderColorMixingFormService->finalize($workOrderColorMixing);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderColorMixingFormService->save($workOrderColorMixing);

            return $this->redirectToRoute('app_production_work_order_color_mixing_show', ['id' => $workOrderColorMixing->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_color_mixing/new.{$_format}.twig", [
            'workOrderColorMixing' => $workOrderColorMixing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_work_order_color_mixing_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderColorMixing $workOrderColorMixing): Response
    {
        return $this->render('production/work_order_color_mixing/show.html.twig', [
            'workOrderColorMixing' => $workOrderColorMixing,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_work_order_color_mixing_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderColorMixing $workOrderColorMixing, WorkOrderColorMixingFormService $workOrderColorMixingFormService, $_format = 'html'): Response
    {
        $workOrderColorMixingFormService->initialize($workOrderColorMixing, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderColorMixingType::class, $workOrderColorMixing);
        $form->handleRequest($request);
        $workOrderColorMixingFormService->finalize($workOrderColorMixing);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderColorMixingFormService->save($workOrderColorMixing);

            return $this->redirectToRoute('app_production_work_order_color_mixing_show', ['id' => $workOrderColorMixing->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_color_mixing/edit.{$_format}.twig", [
            'workOrderColorMixing' => $workOrderColorMixing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_work_order_color_mixing_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderColorMixing $workOrderColorMixing, WorkOrderColorMixingRepository $workOrderColorMixingRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderColorMixing->getId(), $request->request->get('_token'))) {
            $workOrderColorMixingRepository->remove($workOrderColorMixing, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_work_order_color_mixing_index', [], Response::HTTP_SEE_OTHER);
    }
}
