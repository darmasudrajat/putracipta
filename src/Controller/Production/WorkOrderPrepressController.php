<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderPrepress;
use App\Form\Production\WorkOrderPrepressType;
use App\Grid\Production\WorkOrderPrepressGridType;
use App\Repository\Production\WorkOrderPrepressRepository;
use App\Service\Production\WorkOrderPrepressFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/work_order_prepress')]
class WorkOrderPrepressController extends AbstractController
{
    #[Route('/_list', name: 'app_production_work_order_prepress__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderPrepressRepository $workOrderPrepressRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderPrepressGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $workOrderPrepresses) = $workOrderPrepressRepository->fetchData($criteria);

        return $this->renderForm("production/work_order_prepress/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderPrepresses' => $workOrderPrepresses,
        ]);
    }

    #[Route('/', name: 'app_production_work_order_prepress_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/work_order_prepress/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_work_order_prepress_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderPrepressFormService $workOrderPrepressFormService, $_format = 'html'): Response
    {
        $workOrderPrepress = new WorkOrderPrepress();
        $workOrderPrepressFormService->initialize($workOrderPrepress, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderPrepressType::class, $workOrderPrepress);
        $form->handleRequest($request);
        $workOrderPrepressFormService->finalize($workOrderPrepress);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderPrepressFormService->save($workOrderPrepress);

            return $this->redirectToRoute('app_production_work_order_prepress_show', ['id' => $workOrderPrepress->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_prepress/new.{$_format}.twig", [
            'workOrderPrepress' => $workOrderPrepress,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_work_order_prepress_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderPrepress $workOrderPrepress): Response
    {
        return $this->render('production/work_order_prepress/show.html.twig', [
            'workOrderPrepress' => $workOrderPrepress,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_work_order_prepress_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderPrepress $workOrderPrepress, WorkOrderPrepressFormService $workOrderPrepressFormService, $_format = 'html'): Response
    {
        $workOrderPrepressFormService->initialize($workOrderPrepress, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderPrepressType::class, $workOrderPrepress);
        $form->handleRequest($request);
        $workOrderPrepressFormService->finalize($workOrderPrepress);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderPrepressFormService->save($workOrderPrepress);

            return $this->redirectToRoute('app_production_work_order_prepress_show', ['id' => $workOrderPrepress->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_prepress/edit.{$_format}.twig", [
            'workOrderPrepress' => $workOrderPrepress,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_work_order_prepress_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderPrepress $workOrderPrepress, WorkOrderPrepressRepository $workOrderPrepressRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderPrepress->getId(), $request->request->get('_token'))) {
            $workOrderPrepressRepository->remove($workOrderPrepress, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_work_order_prepress_index', [], Response::HTTP_SEE_OTHER);
    }
}
