<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\MaterialCategory;
use App\Form\Master\MaterialCategoryType;
use App\Grid\Master\MaterialCategoryGridType;
use App\Repository\Master\MaterialCategoryRepository;
use App\Service\Master\MaterialCategoryFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/material_category')]
class MaterialCategoryController extends AbstractController
{
    #[Route('/_list', name: 'app_master_material_category__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_MATERIAL_CATEGORY_ADD') or is_granted('ROLE_MATERIAL_CATEGORY_EDIT') or is_granted('ROLE_MATERIAL_CATEGORY_VIEW')")]
    public function _list(Request $request, MaterialCategoryRepository $materialCategoryRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(MaterialCategoryGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $materialCategories) = $materialCategoryRepository->fetchData($criteria);

        return $this->renderForm("master/material_category/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materialCategories' => $materialCategories,
        ]);
    }

    #[Route('/', name: 'app_master_material_category_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_CATEGORY_ADD') or is_granted('ROLE_MATERIAL_CATEGORY_EDIT') or is_granted('ROLE_MATERIAL_CATEGORY_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/material_category/index.html.twig");
    }

    #[Route('/new', name: 'app_master_material_category_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_CATEGORY_ADD')]
    public function new(Request $request, MaterialCategoryFormService $materialCategoryFormService): Response
    {
        $materialCategory = new MaterialCategory();
        $form = $this->createForm(MaterialCategoryType::class, $materialCategory);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $materialCategoryFormService->save($materialCategory);

            return $this->redirectToRoute('app_master_material_category_show', ['id' => $materialCategory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/material_category/new.html.twig', [
            'materialCategory' => $materialCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_material_category_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_CATEGORY_ADD') or is_granted('ROLE_MATERIAL_CATEGORY_EDIT') or is_granted('ROLE_MATERIAL_CATEGORY_VIEW')")]
    public function show(MaterialCategory $materialCategory): Response
    {
        return $this->render('master/material_category/show.html.twig', [
            'materialCategory' => $materialCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_material_category_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_CATEGORY_EDIT')]
    public function edit(Request $request, MaterialCategory $materialCategory, MaterialCategoryFormService $materialCategoryFormService): Response
    {
        $form = $this->createForm(MaterialCategoryType::class, $materialCategory);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $materialCategoryFormService->save($materialCategory);

            return $this->redirectToRoute('app_master_material_category_show', ['id' => $materialCategory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/material_category/edit.html.twig', [
            'materialCategory' => $materialCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_material_category_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MATERIAL_CATEGORY_EDIT')]
    public function delete(Request $request, MaterialCategory $materialCategory, MaterialCategoryRepository $materialCategoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $materialCategory->getId(), $request->request->get('_token'))) {
            $materialCategoryRepository->remove($materialCategory, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_material_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
