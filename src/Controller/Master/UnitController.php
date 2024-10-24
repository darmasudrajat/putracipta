<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Unit;
use App\Form\Master\UnitType;
use App\Grid\Master\UnitGridType;
use App\Repository\Master\UnitRepository;
use App\Service\Master\UnitFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/unit')]
class UnitController extends AbstractController
{
    #[Route('/_list', name: 'app_master_unit__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_UNIT_ADD') or is_granted('ROLE_UNIT_EDIT') or is_granted('ROLE_UNIT_VIEW')")]
    public function _list(Request $request, UnitRepository $unitRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(UnitGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $units) = $unitRepository->fetchData($criteria);

        return $this->renderForm("master/unit/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'units' => $units,
        ]);
    }

    #[Route('/', name: 'app_master_unit_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_UNIT_ADD') or is_granted('ROLE_UNIT_EDIT') or is_granted('ROLE_UNIT_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/unit/index.html.twig");
    }

    #[Route('/new', name: 'app_master_unit_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_UNIT_ADD')]
    public function new(Request $request, UnitFormService $unitFormService): Response
    {
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $unitFormService->save($unit);

            return $this->redirectToRoute('app_master_unit_show', ['id' => $unit->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/unit/new.html.twig', [
            'unit' => $unit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_unit_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_UNIT_ADD') or is_granted('ROLE_UNIT_EDIT') or is_granted('ROLE_UNIT_VIEW')")]
    public function show(Unit $unit): Response
    {
        return $this->render('master/unit/show.html.twig', [
            'unit' => $unit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_unit_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_UNIT_EDIT')]
    public function edit(Request $request, Unit $unit, UnitFormService $unitFormService): Response
    {
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $unitFormService->save($unit);

            return $this->redirectToRoute('app_master_unit_show', ['id' => $unit->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/unit/edit.html.twig', [
            'unit' => $unit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_unit_delete', methods: ['POST'])]
    #[IsGranted('ROLE_UNIT_EDIT')]
    public function delete(Request $request, Unit $unit, UnitRepository $unitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $unit->getId(), $request->request->get('_token'))) {
            $unitRepository->remove($unit, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_unit_index', [], Response::HTTP_SEE_OTHER);
    }
}
