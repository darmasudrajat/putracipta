<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Accounting\AccountingLedger;
use App\Form\Accounting\AccountingLedgerType;
use App\Grid\Accounting\AccountingLedgerGridType;
use App\Repository\Accounting\AccountingLedgerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/accounting_ledger')]
class AccountingLedgerController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_accounting_ledger__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, AccountingLedgerRepository $accountingLedgerRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(AccountingLedgerGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $accountingLedgers) = $accountingLedgerRepository->fetchData($criteria);

        return $this->renderForm("accounting/accounting_ledger/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'accountingLedgers' => $accountingLedgers,
        ]);
    }

    #[Route('/', name: 'app_accounting_accounting_ledger_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("accounting/accounting_ledger/index.html.twig");
    }

    #[Route('/new', name: 'app_accounting_accounting_ledger_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, AccountingLedgerRepository $accountingLedgerRepository): Response
    {
        $accountingLedger = new AccountingLedger();
        $form = $this->createForm(AccountingLedgerType::class, $accountingLedger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountingLedgerRepository->add($accountingLedger, true);

            return $this->redirectToRoute('app_accounting_accounting_ledger_show', ['id' => $accountingLedger->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/accounting_ledger/new.html.twig', [
            'accountingLedger' => $accountingLedger,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_accounting_ledger_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(AccountingLedger $accountingLedger): Response
    {
        return $this->render('accounting/accounting_ledger/show.html.twig', [
            'accountingLedger' => $accountingLedger,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_accounting_accounting_ledger_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, AccountingLedger $accountingLedger, AccountingLedgerRepository $accountingLedgerRepository): Response
    {
        $form = $this->createForm(AccountingLedgerType::class, $accountingLedger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountingLedgerRepository->add($accountingLedger, true);

            return $this->redirectToRoute('app_accounting_accounting_ledger_show', ['id' => $accountingLedger->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/accounting_ledger/edit.html.twig', [
            'accountingLedger' => $accountingLedger,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_accounting_ledger_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, AccountingLedger $accountingLedger, AccountingLedgerRepository $accountingLedgerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $accountingLedger->getId(), $request->request->get('_token'))) {
            $accountingLedgerRepository->remove($accountingLedger, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_accounting_ledger_index', [], Response::HTTP_SEE_OTHER);
    }
}
