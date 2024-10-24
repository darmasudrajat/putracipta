<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Warehouse;
use App\Form\Master\WarehouseType;
use App\Grid\Master\WarehouseGridType;
use App\Repository\Master\WarehouseRepository;
use App\Service\Master\WarehouseFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/warehouse')]
class WarehouseController extends AbstractController
{
    #[Route('/_list', name: 'app_master_warehouse__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_WAREHOUSE_ADD') or is_granted('ROLE_WAREHOUSE_EDIT') or is_granted('ROLE_WAREHOUSE_VIEW')")]
    public function _list(Request $request, WarehouseRepository $warehouseRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WarehouseGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $warehouses) = $warehouseRepository->fetchData($criteria);

        return $this->renderForm("master/warehouse/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'warehouses' => $warehouses,
        ]);
    }

    #[Route('/', name: 'app_master_warehouse_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_WAREHOUSE_ADD') or is_granted('ROLE_WAREHOUSE_EDIT') or is_granted('ROLE_WAREHOUSE_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/warehouse/index.html.twig");
    }

    #[Route('/new', name: 'app_master_warehouse_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_WAREHOUSE_ADD')]
    public function new(Request $request, WarehouseFormService $warehouseFormService): Response
    {
        $warehouse = new Warehouse();
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $warehouseFormService->save($warehouse);

            return $this->redirectToRoute('app_master_warehouse_show', ['id' => $warehouse->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/warehouse/new.html.twig', [
            'warehouse' => $warehouse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_warehouse_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_WAREHOUSE_ADD') or is_granted('ROLE_WAREHOUSE_EDIT') or is_granted('ROLE_WAREHOUSE_VIEW')")]
    public function show(Warehouse $warehouse): Response
    {
        return $this->render('master/warehouse/show.html.twig', [
            'warehouse' => $warehouse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_warehouse_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_WAREHOUSE_EDIT')]
    public function edit(Request $request, Warehouse $warehouse, WarehouseFormService $warehouseFormService): Response
    {
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $warehouseFormService->save($warehouse);

            return $this->redirectToRoute('app_master_warehouse_show', ['id' => $warehouse->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/warehouse/edit.html.twig', [
            'warehouse' => $warehouse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_warehouse_delete', methods: ['POST'])]
    #[IsGranted('ROLE_WAREHOUSE_EDIT')]
    public function delete(Request $request, Warehouse $warehouse, WarehouseRepository $warehouseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $warehouse->getId(), $request->request->get('_token'))) {
            $warehouseRepository->remove($warehouse, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_warehouse_index', [], Response::HTTP_SEE_OTHER);
    }
}
