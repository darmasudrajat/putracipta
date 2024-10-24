<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Supplier;
use App\Form\Master\SupplierType;
use App\Grid\Master\SupplierGridType;
use App\Repository\Master\SupplierRepository;
use App\Repository\Purchase\PurchaseOrderHeaderRepository;
use App\Service\Master\SupplierFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/supplier')]
class SupplierController extends AbstractController
{
    #[Route('/_list', name: 'app_master_supplier__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SUPPLIER_ADD') or is_granted('ROLE_SUPPLIER_EDIT') or is_granted('ROLE_SUPPLIER_VIEW')")]
    public function _list(Request $request, SupplierRepository $supplierRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SupplierGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $suppliers) = $supplierRepository->fetchData($criteria);

        return $this->renderForm("master/supplier/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'suppliers' => $suppliers,
        ]);
    }

    #[Route('/', name: 'app_master_supplier_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_SUPPLIER_ADD') or is_granted('ROLE_SUPPLIER_EDIT') or is_granted('ROLE_SUPPLIER_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/supplier/index.html.twig");
    }

    #[Route('/new', name: 'app_master_supplier_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SUPPLIER_ADD')]
    public function new(Request $request, SupplierFormService $supplierFormService): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $supplierFormService->save($supplier);

            return $this->redirectToRoute('app_master_supplier_show', ['id' => $supplier->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/supplier/new.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_supplier_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_SUPPLIER_ADD') or is_granted('ROLE_SUPPLIER_EDIT') or is_granted('ROLE_SUPPLIER_VIEW')")]
    public function show(Supplier $supplier, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $purchaseOrderHeaders = $purchaseOrderHeaderRepository->findBy(['supplier' => $supplier], ['transactionDate' => 'DESC'], 50);
        
        return $this->render('master/supplier/show.html.twig', [
            'supplier' => $supplier,
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_supplier_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SUPPLIER_EDIT')]
    public function edit(Request $request, Supplier $supplier, SupplierFormService $supplierFormService): Response
    {
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $supplierFormService->save($supplier);

            return $this->redirectToRoute('app_master_supplier_show', ['id' => $supplier->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/supplier/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_supplier_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SUPPLIER_EDIT')]
    public function delete(Request $request, Supplier $supplier, SupplierRepository $supplierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $supplier->getId(), $request->request->get('_token'))) {
            $supplierRepository->remove($supplier, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_supplier_index', [], Response::HTTP_SEE_OTHER);
    }
}
