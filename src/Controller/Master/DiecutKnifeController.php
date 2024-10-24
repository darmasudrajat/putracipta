<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DiecutKnife;
use App\Form\Master\DiecutKnifeType;
use App\Grid\Master\DiecutKnifeGridType;
use App\Repository\Master\DiecutKnifeRepository;
use App\Service\Master\DiecutKnifeFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/diecut_knife')]
class DiecutKnifeController extends AbstractController
{
    #[Route('/_diecut_list', name: 'app_master_diecut_knife__diecut_list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DIECUT_ADD') or is_granted('ROLE_DIECUT_EDIT') or is_granted('ROLE_DIECUT_VIEW')")]
    public function _diecutList(Request $request, DiecutKnifeRepository $diecutKnifeRepository): Response
    {
        $lastDiecutKnives = $diecutKnifeRepository->findBy(['customer' => $request->request->get('diecut_knife')['customer']], ['id' => 'DESC'], 5, 0);

        return $this->render("master/diecut_knife/_diecut_list.html.twig", [
            'lastDiecutKnives' => $lastDiecutKnives,
        ]);
    }

    #[Route('/_list', name: 'app_master_diecut_knife__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DIECUT_ADD') or is_granted('ROLE_DIECUT_EDIT') or is_granted('ROLE_DIECUT_VIEW')")]
    public function _list(Request $request, DiecutKnifeRepository $diecutKnifeRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'date' => SortDescending::class,
            'name' => SortDescending::class,
        ]);
        $form = $this->createForm(DiecutKnifeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $diecutKnives) = $diecutKnifeRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('diecut_knife_grid')['filter']['customer:company']) && isset($request->request->get('diecut_knife_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('diecut_knife_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('diecut_knife_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("master/diecut_knife/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'diecutKnives' => $diecutKnives,
        ]);
    }

    #[Route('/', name: 'app_master_diecut_knife_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_DIECUT_ADD') or is_granted('ROLE_DIECUT_EDIT') or is_granted('ROLE_DIECUT_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/diecut_knife/index.html.twig");
    }

    #[Route('/new', name: 'app_master_diecut_knife_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DIECUT_ADD')]
    public function new(Request $request, DiecutKnifeFormService $diecutKnifeFormService): Response
    {
        $diecutKnife = new DiecutKnife();
        $diecutKnifeFormService->initialize($diecutKnife, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(DiecutKnifeType::class, $diecutKnife);
        $form->handleRequest($request);
        $diecutKnifeFormService->finalize($diecutKnife);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $diecutKnifeFormService->save($diecutKnife, ['sourceDiecutKnife' => null]);

            return $this->redirectToRoute('app_master_diecut_knife_show', ['id' => $diecutKnife->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/diecut_knife/new.html.twig', [
            'diecutKnife' => $diecutKnife,
            'form' => $form,
            'lastDiecutKnives' => [],
        ]);
    }

    #[Route('/{id}', name: 'app_master_diecut_knife_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_DIECUT_ADD') or is_granted('ROLE_DIECUT_EDIT') or is_granted('ROLE_DIECUT_VIEW')")]
    public function show(DiecutKnife $diecutKnife): Response
    {
        return $this->render('master/diecut_knife/show.html.twig', [
            'diecutKnife' => $diecutKnife,
        ]);
    }

    #[Route('/{source_id}/new_repeat.{_format}', name: 'app_master_diecut_knife_new_repeat', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DIECUT_ADD')]
    public function newRepeat(Request $request, DiecutKnifeRepository $diecutKnifeRepository, DiecutKnifeFormService $diecutKnifeFormService, $_format = 'html'): Response
    {
        $sourceDiecutKnife = $diecutKnifeRepository->find($request->attributes->getInt('source_id'));
        $diecutKnife = $diecutKnifeFormService->copyFrom($sourceDiecutKnife);
        $diecutKnifeFormService->initialize($diecutKnife, ['datetime' => new \DateTime(), 'user' => $this->getUser(), 'sourceDiecutKnife' => $sourceDiecutKnife]);
        $form = $this->createForm(DiecutKnifeType::class, $diecutKnife);
        $form->handleRequest($request);
        $diecutKnifeFormService->finalize($diecutKnife);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $diecutKnifeFormService->save($diecutKnife, ['sourceDiecutKnife' => $sourceDiecutKnife]);

            return $this->redirectToRoute('app_master_diecut_knife_show', ['id' => $diecutKnife->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("master/diecut_knife/new_repeat.{$_format}.twig", [
            'diecutKnife' => $diecutKnife,
            'form' => $form,
            'lastDiecutKnives' => $diecutKnifeRepository->findBy(['customer' => $diecutKnife->getCustomer()], ['id' => 'DESC'], 5, 0),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_diecut_knife_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DIECUT_EDIT')]
    public function edit(Request $request, DiecutKnife $diecutKnife, DiecutKnifeRepository $diecutKnifeRepository, DiecutKnifeFormService $diecutKnifeFormService): Response
    {
        $diecutKnifeFormService->initialize($diecutKnife, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(DiecutKnifeType::class, $diecutKnife);
        $form->handleRequest($request);
        $diecutKnifeFormService->finalize($diecutKnife);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $diecutKnifeFormService->save($diecutKnife, ['sourceDiecutKnife' => null]);

            return $this->redirectToRoute('app_master_diecut_knife_show', ['id' => $diecutKnife->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/diecut_knife/edit.html.twig', [
            'diecutKnife' => $diecutKnife,
            'form' => $form,
            'lastDiecutKnives' => $diecutKnifeRepository->findBy(['customer' => $diecutKnife->getCustomer()], ['id' => 'DESC'], 5, 0),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_diecut_knife_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DIECUT_EDIT')]
    public function delete(Request $request, DiecutKnife $diecutKnife, DiecutKnifeRepository $diecutKnifeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $diecutKnife->getId(), $request->request->get('_token'))) {
            $diecutKnifeRepository->remove($diecutKnife, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_diecut_knife_index', [], Response::HTTP_SEE_OTHER);
    }
}
