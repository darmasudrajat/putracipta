<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Grid\Shared\PaperGridType;
use App\Repository\Master\PaperRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/paper')]
class PaperController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_paper__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PaperRepository $paperRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'name' => SortAscending::class,
        ]);
        $form = $this->createForm(PaperGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $papers) = $paperRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('paper_grid')['filter']['unit:name']) && isset($request->request->get('paper_grid')['sort']['unit:name'])) {
                $qb->innerJoin("{$alias}.unit", 'u');
                $add['filter']($qb, 'u', 'name', $request->request->get('paper_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->request->get('paper_grid')['sort']['unit:name']);
            }
            if (isset($request->request->get('paper_grid')['filter']['materialSubCategory:name']) && isset($request->request->get('paper_grid')['sort']['materialSubCategory:name'])) {
                $qb->innerJoin("{$alias}.materialSubCategory", 's');
                $add['filter']($qb, 's', 'name', $request->request->get('paper_grid')['filter']['materialSubCategory:name']);
                $add['sort']($qb, 's', 'name', $request->request->get('paper_grid')['sort']['materialSubCategory:name']);
            }
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/paper/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'papers' => $papers,
        ]);
    }
}
