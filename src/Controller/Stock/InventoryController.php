<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Stock\Inventory;
use App\Form\Stock\InventoryType;
use App\Grid\Stock\InventoryGridType;
use App\Repository\Stock\InventoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/inventory')]
class InventoryController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_inventory__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryRepository $inventoryRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(InventoryGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventories) = $inventoryRepository->fetchData($criteria);

        return $this->renderForm("stock/inventory/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventories' => $inventories,
        ]);
    }

    #[Route('/', name: 'app_stock_inventory_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/inventory/index.html.twig");
    }

    #[Route('/new', name: 'app_stock_inventory_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, InventoryRepository $inventoryRepository): Response
    {
        $inventory = new Inventory();
        $form = $this->createForm(InventoryType::class, $inventory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inventoryRepository->add($inventory, true);

            return $this->redirectToRoute('app_stock_inventory_show', ['id' => $inventory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/inventory/new.html.twig', [
            'inventory' => $inventory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_inventory_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Inventory $inventory): Response
    {
        return $this->render('stock/inventory/show.html.twig', [
            'inventory' => $inventory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_inventory_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Inventory $inventory, InventoryRepository $inventoryRepository): Response
    {
        $form = $this->createForm(InventoryType::class, $inventory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inventoryRepository->add($inventory, true);

            return $this->redirectToRoute('app_stock_inventory_show', ['id' => $inventory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/inventory/edit.html.twig', [
            'inventory' => $inventory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_inventory_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Inventory $inventory, InventoryRepository $inventoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inventory->getId(), $request->request->get('_token'))) {
            $inventoryRepository->remove($inventory, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_inventory_index', [], Response::HTTP_SEE_OTHER);
    }
}
