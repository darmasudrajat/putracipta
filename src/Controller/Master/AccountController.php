<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Account;
use App\Form\Master\AccountType;
use App\Grid\Master\AccountGridType;
use App\Repository\Master\AccountRepository;
use App\Service\Master\AccountFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/account')]
class AccountController extends AbstractController
{
    #[Route('/_list', name: 'app_master_account__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ACCOUNT_ADD') or is_granted('ROLE_ACCOUNT_EDIT')")]
    public function _list(Request $request, AccountRepository $accountRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'name' => SortAscending::class,
        ]);
        $form = $this->createForm(AccountGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $accounts) = $accountRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.accountCategory", 's');
            if (isset($request->request->get('account_grid')['sort']['accountCategory:name'])) {
                $add['sort']($qb, 's', 'name', $request->request->get('account_grid')['sort']['accountCategory:name']);
            }
        });

        return $this->renderForm("master/account/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'accounts' => $accounts,
        ]);
    }

    #[Route('/', name: 'app_master_account_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_ACCOUNT_ADD') or is_granted('ROLE_ACCOUNT_EDIT')")]
    public function index(): Response
    {
        return $this->render("master/account/index.html.twig");
    }

    #[Route('/new', name: 'app_master_account_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ACCOUNT_ADD')]
    public function new(Request $request, AccountFormService $accountFormService): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $accountFormService->save($account);

            return $this->redirectToRoute('app_master_account_show', ['id' => $account->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/account/new.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_account_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_ACCOUNT_ADD') or is_granted('ROLE_ACCOUNT_EDIT')")]
    public function show(Account $account): Response
    {
        return $this->render('master/account/show.html.twig', [
            'account' => $account,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_account_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ACCOUNT_EDIT')]
    public function edit(Request $request, Account $account, AccountFormService $accountFormService): Response
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $accountFormService->save($account);

            return $this->redirectToRoute('app_master_account_show', ['id' => $account->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/account/edit.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_account_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ACCOUNT_EDIT')]
    public function delete(Request $request, Account $account, AccountRepository $accountRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $account->getId(), $request->request->get('_token'))) {
            $accountRepository->remove($account, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
