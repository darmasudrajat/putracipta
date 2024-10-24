<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\InventoryRequestHeader;
use App\Entity\Stock\InventoryRequestMaterialDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use App\Form\Stock\InventoryRequestHeaderType;
use App\Grid\Stock\InventoryRequestHeaderGridType;
use App\Repository\Stock\InventoryRequestHeaderRepository;
use App\Service\Stock\InventoryRequestHeaderFormService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/inventory_request_header')]
class InventoryRequestHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_inventory_request_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_MATERIAL_REQUEST_ADD') or is_granted('ROLE_MATERIAL_REQUEST_EDIT') or is_granted('ROLE_MATERIAL_REQUEST_VIEW')")]
    public function _list(Request $request, InventoryRequestHeaderRepository $inventoryRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(InventoryRequestHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryRequestHeaders) = $inventoryRequestHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
//            if (isset($request->request->get('inventory_request_header_grid')['filter']['warehouse:name']) && isset($request->request->get('inventory_request_header_grid')['sort']['warehouse:name'])) {
//                $qb->innerJoin("{$alias}.warehouse", 'w');
//                $add['filter']($qb, 'w', 'name', $request->request->get('inventory_request_header_grid')['filter']['warehouse:name']);
//                $add['sort']($qb, 'w', 'name', $request->request->get('inventory_request_header_grid')['sort']['warehouse:name']);
//            }
        });

        return $this->renderForm("stock/inventory_request_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryRequestHeaders' => $inventoryRequestHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_inventory_request_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_REQUEST_ADD') or is_granted('ROLE_MATERIAL_REQUEST_EDIT') or is_granted('ROLE_MATERIAL_REQUEST_VIEW')")]
    public function index(): Response
    {
        return $this->render("stock/inventory_request_header/index.html.twig");
    }

    #[Route('/_head', name: 'app_stock_inventory_request_header__head', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_MATERIAL_REQUEST_ADD') or is_granted('ROLE_MATERIAL_REQUEST_EDIT') or is_granted('ROLE_MATERIAL_REQUEST_VIEW')")]
    public function _head(Request $request, InventoryRequestHeaderRepository $inventoryRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $inventoryRequestHeaders) = $inventoryRequestHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("stock/inventory_request_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryRequestHeaders' => $inventoryRequestHeaders,
        ]);
    }

    #[Route('/head', name: 'app_stock_inventory_request_header_head', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_REQUEST_ADD') or is_granted('ROLE_MATERIAL_REQUEST_EDIT') or is_granted('ROLE_MATERIAL_REQUEST_VIEW')")]
    public function head(): Response
    {
        return $this->render("stock/inventory_request_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_stock_inventory_request_header_read', methods: ['POST'])]
    #[Security("is_granted('ROLE_MATERIAL_REQUEST_ADD') or is_granted('ROLE_MATERIAL_REQUEST_EDIT') or is_granted('ROLE_MATERIAL_REQUEST_VIEW')")]
    public function read(Request $request, InventoryRequestHeader $inventoryRequestHeader, InventoryRequestHeaderRepository $inventoryRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $inventoryRequestHeader->getId(), $request->request->get('_token'))) {
            $inventoryRequestHeader->setIsRead(true);
            $inventoryRequestHeaderRepository->add($inventoryRequestHeader, true);
        }

        return $this->redirectToRoute('app_stock_inventory_request_header_show', ['id' => $inventoryRequestHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_stock_inventory_request_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_REQUEST_ADD')]
    public function new(Request $request, InventoryRequestHeaderFormService $inventoryRequestHeaderFormService, $_format = 'html'): Response
    {
        $inventoryRequestHeader = new InventoryRequestHeader();
        $inventoryRequestHeaderFormService->initialize($inventoryRequestHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryRequestHeaderType::class, $inventoryRequestHeader);
        $form->handleRequest($request);
        $inventoryRequestHeaderFormService->finalize($inventoryRequestHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryRequestHeaderFormService->save($inventoryRequestHeader);

            return $this->redirectToRoute('app_stock_inventory_request_header_show', ['id' => $inventoryRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_request_header/new.{$_format}.twig", [
            'inventoryRequestHeader' => $inventoryRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_inventory_request_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_REQUEST_ADD') or is_granted('ROLE_MATERIAL_REQUEST_EDIT') or is_granted('ROLE_MATERIAL_REQUEST_VIEW')")]
    public function show(InventoryRequestHeader $inventoryRequestHeader): Response
    {
        return $this->render('stock/inventory_request_header/show.html.twig', [
            'inventoryRequestHeader' => $inventoryRequestHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_stock_inventory_request_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_REQUEST_EDIT')]
    public function edit(Request $request, InventoryRequestHeader $inventoryRequestHeader, InventoryRequestHeaderFormService $inventoryRequestHeaderFormService, $_format = 'html'): Response
    {
        $inventoryRequestHeaderFormService->initialize($inventoryRequestHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryRequestHeaderType::class, $inventoryRequestHeader);
        $form->handleRequest($request);
        $inventoryRequestHeaderFormService->finalize($inventoryRequestHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryRequestHeaderFormService->save($inventoryRequestHeader);

            return $this->redirectToRoute('app_stock_inventory_request_header_show', ['id' => $inventoryRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_request_header/edit.{$_format}.twig", [
            'inventoryRequestHeader' => $inventoryRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/complete', name: 'app_stock_inventory_request_header_complete', methods: ['POST'])]
    #[IsGranted('ROLE_MATERIAL_REQUEST_EDIT')]
    public function complete(Request $request, InventoryRequestHeader $inventoryRequestHeader, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('complete' . $inventoryRequestHeader->getId(), $request->request->get('_token'))) {
            $inventoryRequestHeaderRepository = $entityManager->getRepository(InventoryRequestHeader::class);
            $inventoryRequestMaterialDetailRepository = $entityManager->getRepository(InventoryRequestMaterialDetail::class);
            $inventoryRequestPaperDetailRepository = $entityManager->getRepository(InventoryRequestPaperDetail::class);
            
            $inventoryRequestHeader->setRequestStatus(InventoryRequestHeader::REQUEST_STATUS_CLOSE);
            $inventoryRequestHeaderRepository->add($inventoryRequestHeader);

            foreach ($inventoryRequestHeader->getInventoryRequestMaterialDetails() as $inventoryRequestMaterialDetail) {
                $inventoryRequestMaterialDetail->setQuantityRemaining(0);
                $inventoryRequestMaterialDetailRepository->add($inventoryRequestMaterialDetail);
            }
            
            foreach ($inventoryRequestHeader->getInventoryRequestPaperDetails() as $inventoryRequestPaperDetail) {
                $inventoryRequestPaperDetail->setQuantityRemaining(0);
                $inventoryRequestPaperDetailRepository->add($inventoryRequestPaperDetail);
            }
            
            $entityManager->flush();
        
            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The request was completed successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to completed the request.'));
        }

        return $this->redirectToRoute('app_stock_inventory_request_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_stock_inventory_request_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MATERIAL_REQUEST_EDIT')]
    public function delete(Request $request, InventoryRequestHeader $inventoryRequestHeader, InventoryRequestHeaderRepository $inventoryRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inventoryRequestHeader->getId(), $request->request->get('_token'))) {
            $inventoryRequestHeaderRepository->remove($inventoryRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_inventory_request_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
