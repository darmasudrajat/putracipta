<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\WorkOrderVarnishSpotHeader;
use App\Form\Production\WorkOrderVarnishSpotHeaderType;
use App\Grid\Production\WorkOrderVarnishSpotHeaderGridType;
use App\Repository\Production\WorkOrderVarnishSpotHeaderRepository;
use App\Service\Production\WorkOrderVarnishSpotHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/work_order_varnish_spot_header')]
class WorkOrderVarnishSpotHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_work_order_varnish_spot_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderVarnishSpotHeaderRepository $workOrderVarnishSpotHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderVarnishSpotHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $workOrderVarnishSpotHeaders) = $workOrderVarnishSpotHeaderRepository->fetchData($criteria);

        return $this->renderForm("production/work_order_varnish_spot_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderVarnishSpotHeaders' => $workOrderVarnishSpotHeaders,
        ]);
    }

    #[Route('/', name: 'app_production_work_order_varnish_spot_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/work_order_varnish_spot_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_work_order_varnish_spot_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderVarnishSpotHeaderFormService $workOrderVarnishSpotHeaderFormService, $_format = 'html'): Response
    {
        $workOrderVarnishSpotHeader = new WorkOrderVarnishSpotHeader();
        $workOrderVarnishSpotHeaderFormService->initialize($workOrderVarnishSpotHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderVarnishSpotHeaderType::class, $workOrderVarnishSpotHeader);
        $form->handleRequest($request);
        $workOrderVarnishSpotHeaderFormService->finalize($workOrderVarnishSpotHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderVarnishSpotHeaderFormService->save($workOrderVarnishSpotHeader);

            return $this->redirectToRoute('app_production_work_order_varnish_spot_header_show', ['id' => $workOrderVarnishSpotHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_varnish_spot_header/new.{$_format}.twig", [
            'workOrderVarnishSpotHeader' => $workOrderVarnishSpotHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_work_order_varnish_spot_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader): Response
    {
        return $this->render('production/work_order_varnish_spot_header/show.html.twig', [
            'workOrderVarnishSpotHeader' => $workOrderVarnishSpotHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_work_order_varnish_spot_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader, WorkOrderVarnishSpotHeaderFormService $workOrderVarnishSpotHeaderFormService, $_format = 'html'): Response
    {
        $workOrderVarnishSpotHeaderFormService->initialize($workOrderVarnishSpotHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderVarnishSpotHeaderType::class, $workOrderVarnishSpotHeader);
        $form->handleRequest($request);
        $workOrderVarnishSpotHeaderFormService->finalize($workOrderVarnishSpotHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $workOrderVarnishSpotHeaderFormService->save($workOrderVarnishSpotHeader);

            return $this->redirectToRoute('app_production_work_order_varnish_spot_header_show', ['id' => $workOrderVarnishSpotHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_varnish_spot_header/edit.{$_format}.twig", [
            'workOrderVarnishSpotHeader' => $workOrderVarnishSpotHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_work_order_varnish_spot_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader, WorkOrderVarnishSpotHeaderRepository $workOrderVarnishSpotHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderVarnishSpotHeader->getId(), $request->request->get('_token'))) {
            $workOrderVarnishSpotHeaderRepository->remove($workOrderVarnishSpotHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_work_order_varnish_spot_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
