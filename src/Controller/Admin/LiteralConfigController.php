<?php

namespace App\Controller\Admin;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Admin\LiteralConfig;
use App\Form\Admin\LiteralConfigType;
use App\Grid\Admin\LiteralConfigGridType;
use App\Repository\Admin\LiteralConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/literal_config')]
class LiteralConfigController extends AbstractController
{
    #[Route('/_list', name: 'app_admin_literal_config__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SETTING')]
    public function _list(Request $request, LiteralConfigRepository $literalConfigRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(LiteralConfigGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $literalConfigs) = $literalConfigRepository->fetchData($criteria);

        return $this->renderForm("admin/literal_config/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'literalConfigs' => $literalConfigs,
        ]);
    }

    #[Route('/', name: 'app_admin_literal_config_index', methods: ['GET'])]
    #[IsGranted('ROLE_SETTING')]
    public function index(): Response
    {
        return $this->render("admin/literal_config/index.html.twig");
    }

//    #[Route('/new', name: 'app_admin_literal_config_new', methods: ['GET', 'POST'])]
//    #[IsGranted('ROLE_USER')]
//    public function new(Request $request, LiteralConfigRepository $literalConfigRepository): Response
//    {
//        $literalConfig = new LiteralConfig();
//        $form = $this->createForm(LiteralConfigType::class, $literalConfig);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $literalConfigRepository->add($literalConfig, true);
//
//            return $this->redirectToRoute('app_admin_literal_config_show', ['id' => $literalConfig->getId()], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('admin/literal_config/new.html.twig', [
//            'literalConfig' => $literalConfig,
//            'form' => $form,
//        ]);
//    }

    #[Route('/{id}', name: 'app_admin_literal_config_show', methods: ['GET'])]
    #[IsGranted('ROLE_SETTING')]
    public function show(LiteralConfig $literalConfig): Response
    {
        return $this->render('admin/literal_config/show.html.twig', [
            'literalConfig' => $literalConfig,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_literal_config_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SETTING')]
    public function edit(Request $request, LiteralConfig $literalConfig, LiteralConfigRepository $literalConfigRepository): Response
    {
        $form = $this->createForm(LiteralConfigType::class, $literalConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $literalConfigRepository->add($literalConfig, true);

            return $this->redirectToRoute('app_admin_literal_config_show', ['id' => $literalConfig->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/literal_config/edit.html.twig', [
            'literalConfig' => $literalConfig,
            'form' => $form,
        ]);
    }

//    #[Route('/{id}/delete', name: 'app_admin_literal_config_delete', methods: ['POST'])]
//    #[IsGranted('ROLE_USER')]
//    public function delete(Request $request, LiteralConfig $literalConfig, LiteralConfigRepository $literalConfigRepository): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $literalConfig->getId(), $request->request->get('_token'))) {
//            $literalConfigRepository->remove($literalConfig, true);
//
//            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
//        } else {
//            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
//        }
//
//        return $this->redirectToRoute('app_admin_literal_config_index', [], Response::HTTP_SEE_OTHER);
//    }
}
