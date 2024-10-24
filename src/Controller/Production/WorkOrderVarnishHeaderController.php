<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderVarnishHeader;
use App\Form\Production\WorkOrderVarnishHeaderType;
use App\Grid\Production\WorkOrderVarnishHeaderGridType;
use App\Repository\Production\WorkOrderVarnishHeaderRepository;
use App\Service\Production\WorkOrderVarnishHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/work_order_varnish_header')]
class WorkOrderVarnishHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_work_order_varnish_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderVarnishHeaderRepository $workOrderVarnishHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderVarnishHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $workOrderVarnishHeaders) = $workOrderVarnishHeaderRepository->fetchData($criteria);

        return $this->renderForm("production/work_order_varnish_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderVarnishHeaders' => $workOrderVarnishHeaders,
        ]);
    }

    #[Route('/', name: 'app_production_work_order_varnish_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/work_order_varnish_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_work_order_varnish_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderVarnishHeaderFormService $workOrderVarnishHeaderFormService, $_format = 'html'): Response
    {
        $workOrderVarnishHeader = new WorkOrderVarnishHeader();
        $workOrderVarnishHeaderFormService->initialize($workOrderVarnishHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderVarnishHeaderType::class, $workOrderVarnishHeader);
        $form->handleRequest($request);
        $workOrderVarnishHeaderFormService->finalize($workOrderVarnishHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderVarnishHeaderFormService->save($workOrderVarnishHeader);

            return $this->redirectToRoute('app_production_work_order_varnish_header_show', ['id' => $workOrderVarnishHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_varnish_header/new.{$_format}.twig", [
            'workOrderVarnishHeader' => $workOrderVarnishHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_work_order_varnish_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderVarnishHeader $workOrderVarnishHeader): Response
    {
        return $this->render('production/work_order_varnish_header/show.html.twig', [
            'workOrderVarnishHeader' => $workOrderVarnishHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_work_order_varnish_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderVarnishHeader $workOrderVarnishHeader, WorkOrderVarnishHeaderFormService $workOrderVarnishHeaderFormService, $_format = 'html'): Response
    {
        $workOrderVarnishHeaderFormService->initialize($workOrderVarnishHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderVarnishHeaderType::class, $workOrderVarnishHeader);
        $form->handleRequest($request);
        $workOrderVarnishHeaderFormService->finalize($workOrderVarnishHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderVarnishHeaderFormService->save($workOrderVarnishHeader);

            return $this->redirectToRoute('app_production_work_order_varnish_header_show', ['id' => $workOrderVarnishHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_varnish_header/edit.{$_format}.twig", [
            'workOrderVarnishHeader' => $workOrderVarnishHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_work_order_varnish_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderVarnishHeader $workOrderVarnishHeader, WorkOrderVarnishHeaderRepository $workOrderVarnishHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderVarnishHeader->getId(), $request->request->get('_token'))) {
            $workOrderVarnishHeaderRepository->remove($workOrderVarnishHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_work_order_varnish_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
