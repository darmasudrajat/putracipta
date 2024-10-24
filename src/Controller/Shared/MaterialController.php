<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Grid\Shared\MaterialGridType;
use App\Repository\Master\MaterialRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/material')]
class MaterialController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_material__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MaterialRepository $materialRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'name' => SortAscending::class,
        ]);
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
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/material/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materials' => $materials,
        ]);
    }
}
