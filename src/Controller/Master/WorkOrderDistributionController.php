<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\WorkOrderDistribution;
use App\Form\Master\WorkOrderDistributionType;
use App\Grid\Master\WorkOrderDistributionGridType;
use App\Repository\Master\WorkOrderDistributionRepository;
use App\Service\Master\WorkOrderDistributionFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/work_order_distribution')]
class WorkOrderDistributionController extends AbstractController
{
    #[Route('/_list', name: 'app_master_work_order_distribution__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DISTRIBUTION_ADD') or is_granted('ROLE_DISTRIBUTION_EDIT') or is_granted('ROLE_DISTRIBUTION_VIEW')")]
    public function _list(Request $request, WorkOrderDistributionRepository $workOrderDistributionRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderDistributionGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $workOrderDistributions) = $workOrderDistributionRepository->fetchData($criteria);

        return $this->renderForm("master/work_order_distribution/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderDistributions' => $workOrderDistributions,
        ]);
    }

    #[Route('/', name: 'app_master_work_order_distribution_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_DISTRIBUTION_ADD') or is_granted('ROLE_DISTRIBUTION_EDIT') or is_granted('ROLE_DISTRIBUTION_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/work_order_distribution/index.html.twig");
    }

    #[Route('/new', name: 'app_master_work_order_distribution_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DISTRIBUTION_ADD')]
    public function new(Request $request, WorkOrderDistributionFormService $workOrderDistributionFormService): Response
    {
        $workOrderDistribution = new WorkOrderDistribution();
        $form = $this->createForm(WorkOrderDistributionType::class, $workOrderDistribution);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderDistributionFormService->save($workOrderDistribution);

            return $this->redirectToRoute('app_master_work_order_distribution_show', ['id' => $workOrderDistribution->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/work_order_distribution/new.html.twig', [
            'workOrderDistribution' => $workOrderDistribution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_work_order_distribution_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_DISTRIBUTION_ADD') or is_granted('ROLE_DISTRIBUTION_EDIT') or is_granted('ROLE_DISTRIBUTION_VIEW')")]
    public function show(WorkOrderDistribution $workOrderDistribution): Response
    {
        return $this->render('master/work_order_distribution/show.html.twig', [
            'workOrderDistribution' => $workOrderDistribution,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_work_order_distribution_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DISTRIBUTION_EDIT')]
    public function edit(Request $request, WorkOrderDistribution $workOrderDistribution, WorkOrderDistributionFormService $workOrderDistributionFormService): Response
    {
        $form = $this->createForm(WorkOrderDistributionType::class, $workOrderDistribution);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderDistributionFormService->save($workOrderDistribution);

            return $this->redirectToRoute('app_master_work_order_distribution_show', ['id' => $workOrderDistribution->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/work_order_distribution/edit.html.twig', [
            'workOrderDistribution' => $workOrderDistribution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_work_order_distribution_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DISTRIBUTION_EDIT')]
    public function delete(Request $request, WorkOrderDistribution $workOrderDistribution, WorkOrderDistributionRepository $workOrderDistributionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderDistribution->getId(), $request->request->get('_token'))) {
            $workOrderDistributionRepository->remove($workOrderDistribution, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_work_order_distribution_index', [], Response::HTTP_SEE_OTHER);
    }
}
