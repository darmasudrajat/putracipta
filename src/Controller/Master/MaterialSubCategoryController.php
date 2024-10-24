<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\MaterialSubCategory;
use App\Form\Master\MaterialSubCategoryType;
use App\Grid\Master\MaterialSubCategoryGridType;
use App\Repository\Master\MaterialSubCategoryRepository;
use App\Service\Master\MaterialSubCategoryFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/material_sub_category')]
class MaterialSubCategoryController extends AbstractController
{
    #[Route('/_list', name: 'app_master_material_sub_category__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_MATERIAL_SUB_CATEGORY_ADD') or is_granted('ROLE_MATERIAL_SUB_CATEGORY_EDIT') or is_granted('ROLE_MATERIAL_SUB_CATEGORY_VIEW')")]
    public function _list(Request $request, MaterialSubCategoryRepository $materialSubCategoryRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(MaterialSubCategoryGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $materialSubCategories) = $materialSubCategoryRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('material_sub_category_grid')['filter']['materialCategory:name']) && isset($request->request->get('material_sub_category_grid')['sort']['materialCategory:name'])) {
                $qb->innerJoin("{$alias}.materialCategory", 'c');
                $add['filter']($qb, 'c', 'name', $request->request->get('material_sub_category_grid')['filter']['materialCategory:name']);
                $add['sort']($qb, 'c', 'name', $request->request->get('material_sub_category_grid')['sort']['materialCategory:name']);
            }
        });

        return $this->renderForm("master/material_sub_category/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materialSubCategories' => $materialSubCategories,
        ]);
    }

    #[Route('/', name: 'app_master_material_sub_category_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_SUB_CATEGORY_ADD') or is_granted('ROLE_MATERIAL_SUB_CATEGORY_EDIT') or is_granted('ROLE_MATERIAL_SUB_CATEGORY_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/material_sub_category/index.html.twig");
    }

    #[Route('/new', name: 'app_master_material_sub_category_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_SUB_CATEGORY_ADD')]
    public function new(Request $request, MaterialSubCategoryFormService $materialSubCategoryFormService): Response
    {
        $materialSubCategory = new MaterialSubCategory();
        $form = $this->createForm(MaterialSubCategoryType::class, $materialSubCategory);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $materialSubCategoryFormService->save($materialSubCategory);

            return $this->redirectToRoute('app_master_material_sub_category_show', ['id' => $materialSubCategory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/material_sub_category/new.html.twig', [
            'materialSubCategory' => $materialSubCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_material_sub_category_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_SUB_CATEGORY_ADD') or is_granted('ROLE_MATERIAL_SUB_CATEGORY_EDIT') or is_granted('ROLE_MATERIAL_SUB_CATEGORY_VIEW')")]
    public function show(MaterialSubCategory $materialSubCategory): Response
    {
        return $this->render('master/material_sub_category/show.html.twig', [
            'materialSubCategory' => $materialSubCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_material_sub_category_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_SUB_CATEGORY_EDIT')]
    public function edit(Request $request, MaterialSubCategory $materialSubCategory, MaterialSubCategoryFormService $materialSubCategoryFormService): Response
    {
        $form = $this->createForm(MaterialSubCategoryType::class, $materialSubCategory);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $materialSubCategoryFormService->save($materialSubCategory);

            return $this->redirectToRoute('app_master_material_sub_category_show', ['id' => $materialSubCategory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/material_sub_category/edit.html.twig', [
            'materialSubCategory' => $materialSubCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_material_sub_category_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MATERIAL_SUB_CATEGORY_EDIT')]
    public function delete(Request $request, MaterialSubCategory $materialSubCategory, MaterialSubCategoryRepository $materialSubCategoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $materialSubCategory->getId(), $request->request->get('_token'))) {
            $materialSubCategoryRepository->remove($materialSubCategory, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_material_sub_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
