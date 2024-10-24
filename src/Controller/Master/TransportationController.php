<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Transportation;
use App\Form\Master\TransportationType;
use App\Grid\Master\TransportationGridType;
use App\Repository\Master\TransportationRepository;
use App\Service\Master\TransportationFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/transportation')]
class TransportationController extends AbstractController
{
    #[Route('/_list', name: 'app_master_transportation__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_TRANSPORTATION_ADD') or is_granted('ROLE_TRANSPORTATION_EDIT') or is_granted('ROLE_TRANSPORTATION_VIEW')")]
    public function _list(Request $request, TransportationRepository $transportationRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(TransportationGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $transportations) = $transportationRepository->fetchData($criteria);

        return $this->renderForm("master/transportation/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'transportations' => $transportations,
        ]);
    }

    #[Route('/', name: 'app_master_transportation_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_TRANSPORTATION_ADD') or is_granted('ROLE_TRANSPORTATION_EDIT') or is_granted('ROLE_TRANSPORTATION_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/transportation/index.html.twig");
    }

    #[Route('/new', name: 'app_master_transportation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TRANSPORTATION_ADD')]
    public function new(Request $request, TransportationFormService $transportationFormService): Response
    {
        $transportation = new Transportation();
        $form = $this->createForm(TransportationType::class, $transportation);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $transportationFormService->save($transportation);

            return $this->redirectToRoute('app_master_transportation_show', ['id' => $transportation->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/transportation/new.html.twig', [
            'transportation' => $transportation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_transportation_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_TRANSPORTATION_ADD') or is_granted('ROLE_TRANSPORTATION_EDIT') or is_granted('ROLE_TRANSPORTATION_VIEW')")]
    public function show(Transportation $transportation): Response
    {
        return $this->render('master/transportation/show.html.twig', [
            'transportation' => $transportation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_transportation_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TRANSPORTATION_EDIT')]
    public function edit(Request $request, Transportation $transportation, TransportationFormService $transportationFormService): Response
    {
        $form = $this->createForm(TransportationType::class, $transportation);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $transportationFormService->save($transportation);

            return $this->redirectToRoute('app_master_transportation_show', ['id' => $transportation->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/transportation/edit.html.twig', [
            'transportation' => $transportation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_transportation_delete', methods: ['POST'])]
    #[IsGranted('ROLE_TRANSPORTATION_EDIT')]
    public function delete(Request $request, Transportation $transportation, TransportationRepository $transportationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $transportation->getId(), $request->request->get('_token'))) {
            $transportationRepository->remove($transportation, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_transportation_index', [], Response::HTTP_SEE_OTHER);
    }
}
