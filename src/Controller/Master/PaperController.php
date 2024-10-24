<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Paper;
use App\Form\Master\PaperType;
use App\Grid\Master\PaperGridType;
use App\Repository\Master\PaperRepository;
use App\Service\Master\PaperFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/paper')]
class PaperController extends AbstractController
{
    #[Route('/_list', name: 'app_master_paper__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PAPER_ADD') or is_granted('ROLE_PAPER_EDIT') or is_granted('ROLE_PAPER_VIEW')")]
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
        });

        return $this->renderForm("master/paper/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'papers' => $papers,
        ]);
    }

    #[Route('/', name: 'app_master_paper_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_PAPER_ADD') or is_granted('ROLE_PAPER_EDIT') or is_granted('ROLE_PAPER_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/paper/index.html.twig");
    }

    #[Route('/new', name: 'app_master_paper_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PAPER_ADD')]
    public function new(Request $request, PaperFormService $paperFormService): Response
    {
        $paper = new Paper();
        $form = $this->createForm(PaperType::class, $paper);
        $form->handleRequest($request);
        $paperFormService->finalize($paper, ['materialSubCategory' => null, 'weight' => '', 'type' => '']);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $paperFormService->save($paper);

            return $this->redirectToRoute('app_master_paper_show', ['id' => $paper->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/paper/new.html.twig', [
            'paper' => $paper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_paper_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_PAPER_ADD') or is_granted('ROLE_PAPER_EDIT') or is_granted('ROLE_PAPER_VIEW')")]
    public function show(Paper $paper): Response
    {
        return $this->render('master/paper/show.html.twig', [
            'paper' => $paper,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_paper_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PAPER_EDIT')]
    public function edit(Request $request, Paper $paper, PaperFormService $paperFormService): Response
    {
        $materialSubCategory = $paper->getMaterialSubCategory();
        $weight = $paper->getWeight();
        $type = $paper->getType();
        $form = $this->createForm(PaperType::class, $paper);
        $form->handleRequest($request);
        $paperFormService->finalize($paper, ['materialSubCategory' => $materialSubCategory, 'weight' => $weight, 'type' => $type]);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $paperFormService->save($paper);

            return $this->redirectToRoute('app_master_paper_show', ['id' => $paper->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/paper/edit.html.twig', [
            'paper' => $paper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_paper_delete', methods: ['POST'])]
    #[IsGranted('ROLE_PAPER_EDIT')]
    public function delete(Request $request, Paper $paper, PaperRepository $paperRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $paper->getId(), $request->request->get('_token'))) {
            $paperRepository->remove($paper, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_paper_index', [], Response::HTTP_SEE_OTHER);
    }
}
