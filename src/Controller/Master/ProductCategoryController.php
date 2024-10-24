<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\ProductCategory;
use App\Form\Master\ProductCategoryType;
use App\Grid\Master\ProductCategoryGridType;
use App\Repository\Master\ProductCategoryRepository;
use App\Service\Master\ProductCategoryFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/product_category')]
class ProductCategoryController extends AbstractController
{
    #[Route('/_list', name: 'app_master_product_category__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ProductCategoryRepository $productCategoryRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ProductCategoryGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $productCategories) = $productCategoryRepository->fetchData($criteria);

        return $this->renderForm("master/product_category/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'productCategories' => $productCategories,
        ]);
    }

    #[Route('/', name: 'app_master_product_category_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/product_category/index.html.twig");
    }

    #[Route('/new', name: 'app_master_product_category_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ProductCategoryFormService $productCategoryFormService): Response
    {
        $productCategory = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $productCategoryFormService->save($productCategory);

            return $this->redirectToRoute('app_master_product_category_show', ['id' => $productCategory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/product_category/new.html.twig', [
            'productCategory' => $productCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_product_category_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(ProductCategory $productCategory): Response
    {
        return $this->render('master/product_category/show.html.twig', [
            'productCategory' => $productCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_product_category_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ProductCategory $productCategory, ProductCategoryFormService $productCategoryFormService): Response
    {
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $productCategoryFormService->save($productCategory);

            return $this->redirectToRoute('app_master_product_category_show', ['id' => $productCategory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/product_category/edit.html.twig', [
            'productCategory' => $productCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_product_category_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ProductCategory $productCategory, ProductCategoryRepository $productCategoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $productCategory->getId(), $request->request->get('_token'))) {
            $productCategoryRepository->remove($productCategory, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_product_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
