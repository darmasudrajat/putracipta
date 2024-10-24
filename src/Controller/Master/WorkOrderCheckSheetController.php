<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\WorkOrderCheckSheet;
use App\Form\Master\WorkOrderCheckSheetType;
use App\Grid\Master\WorkOrderCheckSheetGridType;
use App\Repository\Master\WorkOrderCheckSheetRepository;
use App\Service\Master\WorkOrderCheckSheetFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/work_order_check_sheet')]
class WorkOrderCheckSheetController extends AbstractController
{
    #[Route('/_list', name: 'app_master_work_order_check_sheet__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CHECK_SHEET_ADD') or is_granted('ROLE_CHECK_SHEET_EDIT') or is_granted('ROLE_CHECK_SHEET_VIEW')")]
    public function _list(Request $request, WorkOrderCheckSheetRepository $workOrderCheckSheetRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderCheckSheetGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $workOrderCheckSheets) = $workOrderCheckSheetRepository->fetchData($criteria);

        return $this->renderForm("master/work_order_check_sheet/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderCheckSheets' => $workOrderCheckSheets,
        ]);
    }

    #[Route('/', name: 'app_master_work_order_check_sheet_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_CHECK_SHEET_ADD') or is_granted('ROLE_CHECK_SHEET_EDIT') or is_granted('ROLE_CHECK_SHEET_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/work_order_check_sheet/index.html.twig");
    }

    #[Route('/new', name: 'app_master_work_order_check_sheet_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CHECK_SHEET_ADD')]
    public function new(Request $request, WorkOrderCheckSheetFormService $workOrderCheckSheetFormService): Response
    {
        $workOrderCheckSheet = new WorkOrderCheckSheet();
        $form = $this->createForm(WorkOrderCheckSheetType::class, $workOrderCheckSheet);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderCheckSheetFormService->save($workOrderCheckSheet);

            return $this->redirectToRoute('app_master_work_order_check_sheet_show', ['id' => $workOrderCheckSheet->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/work_order_check_sheet/new.html.twig', [
            'workOrderCheckSheet' => $workOrderCheckSheet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_work_order_check_sheet_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_CHECK_SHEET_ADD') or is_granted('ROLE_CHECK_SHEET_EDIT') or is_granted('ROLE_CHECK_SHEET_VIEW')")]
    public function show(WorkOrderCheckSheet $workOrderCheckSheet): Response
    {
        return $this->render('master/work_order_check_sheet/show.html.twig', [
            'workOrderCheckSheet' => $workOrderCheckSheet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_work_order_check_sheet_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CHECK_SHEET_EDIT')]
    public function edit(Request $request, WorkOrderCheckSheet $workOrderCheckSheet, WorkOrderCheckSheetFormService $workOrderCheckSheetFormService): Response
    {
        $form = $this->createForm(WorkOrderCheckSheetType::class, $workOrderCheckSheet);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderCheckSheetFormService->save($workOrderCheckSheet);

            return $this->redirectToRoute('app_master_work_order_check_sheet_show', ['id' => $workOrderCheckSheet->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/work_order_check_sheet/edit.html.twig', [
            'workOrderCheckSheet' => $workOrderCheckSheet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_work_order_check_sheet_delete', methods: ['POST'])]
    #[IsGranted('ROLE_CHECK_SHEET_EDIT')]
    public function delete(Request $request, WorkOrderCheckSheet $workOrderCheckSheet, WorkOrderCheckSheetRepository $workOrderCheckSheetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderCheckSheet->getId(), $request->request->get('_token'))) {
            $workOrderCheckSheetRepository->remove($workOrderCheckSheet, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_work_order_check_sheet_index', [], Response::HTTP_SEE_OTHER);
    }
}
