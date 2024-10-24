<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Criteria\DataCriteriaPagination;
use App\Common\Data\Operator\SortAscending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Material;
use App\Form\Master\MaterialType;
use App\Grid\Master\MaterialGridType;
use App\Repository\Master\MaterialRepository;
use App\Service\Master\MaterialFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/material')]
class MaterialController extends AbstractController
{
    #[Route('/_list', name: 'app_master_material__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_MATERIAL_ADD') or is_granted('ROLE_MATERIAL_EDIT') or is_granted('ROLE_MATERIAL_VIEW')")]
    public function _list(Request $request, MaterialRepository $materialRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'name' => SortAscending::class,
        ]);
        $criteriaPagination = new DataCriteriaPagination();
        $criteriaPagination->setSize(100);
        $criteria->setPagination($criteriaPagination);
        $form = $this->createForm(MaterialGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $materials) = $materialRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.materialSubCategory", 's');
            $qb->innerJoin("s.materialCategory", 'c');
            if (isset($request->request->get('material_grid')['sort']['materialSubCategory:name'])) {
                $add['sort']($qb, 's', 'name', $request->request->get('material_grid')['sort']['materialSubCategory:name']);
            }
            if (isset($request->request->get('material_grid')['filter']['materialSubCategory:materialCategory'])) {
                $add['filter']($qb, 's', 'materialCategory', $request->request->get('material_grid')['filter']['materialSubCategory:materialCategory']);
            }
            if (isset($request->request->get('material_grid')['sort']['materialCategory:name'])) {
                $add['sort']($qb, 'c', 'name', $request->request->get('material_grid')['sort']['materialCategory:name']);
            }
        });

        return $this->renderForm("master/material/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materials' => $materials,
        ]);
    }

    #[Route('/', name: 'app_master_material_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_ADD') or is_granted('ROLE_MATERIAL_EDIT') or is_granted('ROLE_MATERIAL_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/material/index.html.twig");
    }

    #[Route('/new', name: 'app_master_material_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_ADD')]
    public function new(Request $request, MaterialFormService $materialFormService): Response
    {
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);
        $materialFormService->finalize($material, ['oldMaterialSubCategory' => null]);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $materialFormService->save($material);

            return $this->redirectToRoute('app_master_material_show', ['id' => $material->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/material/new.html.twig', [
            'material' => $material,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_material_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_ADD') or is_granted('ROLE_MATERIAL_EDIT') or is_granted('ROLE_MATERIAL_VIEW')")]
    public function show(Material $material): Response
    {
        return $this->render('master/material/show.html.twig', [
            'material' => $material,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_material_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_EDIT')]
    public function edit(Request $request, Material $material, MaterialFormService $materialFormService): Response
    {
        $oldMaterialSubCategory = $material->getMaterialSubCategory();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);
        $materialFormService->finalize($material, ['oldMaterialSubCategory' => $oldMaterialSubCategory]);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $materialFormService->save($material);

            return $this->redirectToRoute('app_master_material_show', ['id' => $material->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/material/edit.html.twig', [
            'material' => $material,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_material_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MATERIAL_EDIT')]
    public function delete(Request $request, Material $material, MaterialRepository $materialRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $material->getId(), $request->request->get('_token'))) {
            $materialRepository->remove($material, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_material_index', [], Response::HTTP_SEE_OTHER);
    }
}
