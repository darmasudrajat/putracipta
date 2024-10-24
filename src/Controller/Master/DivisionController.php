<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\Division;
use App\Form\Master\DivisionType;
use App\Grid\Master\DivisionGridType;
use App\Repository\Master\DivisionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/division')]
class DivisionController extends AbstractController
{
    #[Route('/_list', name: 'app_master_division__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DIVISION_ADD') or is_granted('ROLE_DIVISION_EDIT') or is_granted('ROLE_DIVISION_VIEW')")]
    public function _list(Request $request, DivisionRepository $divisionRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(DivisionGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $divisions) = $divisionRepository->fetchData($criteria);

        return $this->renderForm("master/division/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'divisions' => $divisions,
        ]);
    }

    #[Route('/', name: 'app_master_division_index', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DIVISION_ADD') or is_granted('ROLE_DIVISION_EDIT') or is_granted('ROLE_DIVISION_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/division/index.html.twig");
    }

    #[Route('/new', name: 'app_master_division_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DIVISION_ADD')]
    public function new(Request $request, DivisionRepository $divisionRepository): Response
    {
        $division = new Division();
        $form = $this->createForm(DivisionType::class, $division);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $divisionRepository->add($division, true);

            return $this->redirectToRoute('app_master_division_show', ['id' => $division->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/division/new.html.twig', [
            'division' => $division,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_division_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_DIVISION_ADD') or is_granted('ROLE_DIVISION_EDIT') or is_granted('ROLE_DIVISION_VIEW')")]
    public function show(Division $division): Response
    {
        return $this->render('master/division/show.html.twig', [
            'division' => $division,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_division_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DIVISION_EDIT')]
    public function edit(Request $request, Division $division, DivisionRepository $divisionRepository): Response
    {
        $form = $this->createForm(DivisionType::class, $division);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $divisionRepository->add($division, true);

            return $this->redirectToRoute('app_master_division_show', ['id' => $division->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/division/edit.html.twig', [
            'division' => $division,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_division_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DIVISION_EDIT')]
    public function delete(Request $request, Division $division, DivisionRepository $divisionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $division->getId(), $request->request->get('_token'))) {
            $divisionRepository->remove($division, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_division_index', [], Response::HTTP_SEE_OTHER);
    }
}
