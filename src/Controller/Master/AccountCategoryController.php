<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\AccountCategory;
use App\Form\Master\AccountCategoryType;
use App\Grid\Master\AccountCategoryGridType;
use App\Repository\Master\AccountCategoryRepository;
use App\Service\Master\AccountCategoryFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/account_category')]
class AccountCategoryController extends AbstractController
{
    #[Route('/_list', name: 'app_master_account_category__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, AccountCategoryRepository $accountCategoryRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(AccountCategoryGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $accountCategories) = $accountCategoryRepository->fetchData($criteria);

        return $this->renderForm("master/account_category/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'accountCategories' => $accountCategories,
        ]);
    }

    #[Route('/', name: 'app_master_account_category_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/account_category/index.html.twig");
    }

    #[Route('/new', name: 'app_master_account_category_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, AccountCategoryFormService $ccountCategoryFormService): Response
    {
        $accountCategory = new AccountCategory();
        $form = $this->createForm(AccountCategoryType::class, $accountCategory);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $ccountCategoryFormService->save($accountCategory);

            return $this->redirectToRoute('app_master_account_category_show', ['id' => $accountCategory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/account_category/new.html.twig', [
            'accountCategory' => $accountCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_account_category_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(AccountCategory $accountCategory): Response
    {
        return $this->render('master/account_category/show.html.twig', [
            'accountCategory' => $accountCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_account_category_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, AccountCategory $accountCategory, AccountCategoryFormService $ccountCategoryFormService): Response
    {
        $form = $this->createForm(AccountCategoryType::class, $accountCategory);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $ccountCategoryFormService->save($accountCategory);

            return $this->redirectToRoute('app_master_account_category_show', ['id' => $accountCategory->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/account_category/edit.html.twig', [
            'accountCategory' => $accountCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_account_category_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, AccountCategory $accountCategory, AccountCategoryRepository $accountCategoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $accountCategory->getId(), $request->request->get('_token'))) {
            $accountCategoryRepository->remove($accountCategory, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_account_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
