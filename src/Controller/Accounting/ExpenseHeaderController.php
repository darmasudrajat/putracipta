<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Accounting\ExpenseHeader;
use App\Form\Accounting\ExpenseHeaderType;
use App\Grid\Accounting\ExpenseHeaderGridType;
use App\Repository\Accounting\ExpenseHeaderRepository;
use App\Service\Accounting\ExpenseHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/expense_header')]
class ExpenseHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_expense_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_EXPENSE_ADD') or is_granted('ROLE_EXPENSE_EDIT') or is_granted('ROLE_EXPENSE_VIEW')")]
    public function _list(Request $request, ExpenseHeaderRepository $expenseHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(ExpenseHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $expenseHeaders) = $expenseHeaderRepository->fetchData($criteria);

        return $this->renderForm("accounting/expense_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'expenseHeaders' => $expenseHeaders,
        ]);
    }

    #[Route('/', name: 'app_accounting_expense_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_EXPENSE_ADD') or is_granted('ROLE_EXPENSE_EDIT') or is_granted('ROLE_EXPENSE_VIEW')")]
    public function index(): Response
    {
        return $this->render("accounting/expense_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_accounting_expense_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EXPENSE_ADD')]
    public function new(Request $request, ExpenseHeaderFormService $expenseHeaderFormService, $_format = 'html'): Response
    {
        $expenseHeader = new ExpenseHeader();
        $expenseHeaderFormService->initialize($expenseHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ExpenseHeaderType::class, $expenseHeader);
        $form->handleRequest($request);
        $expenseHeaderFormService->finalize($expenseHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $expenseHeaderFormService->save($expenseHeader);

            return $this->redirectToRoute('app_accounting_expense_header_show', ['id' => $expenseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("accounting/expense_header/new.{$_format}.twig", [
            'expenseHeader' => $expenseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_expense_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_EXPENSE_ADD') or is_granted('ROLE_EXPENSE_EDIT') or is_granted('ROLE_EXPENSE_VIEW')")]
    public function show(ExpenseHeader $expenseHeader): Response
    {
        return $this->render('accounting/expense_header/show.html.twig', [
            'expenseHeader' => $expenseHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_accounting_expense_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EXPENSE_EDIT')]
    public function edit(Request $request, ExpenseHeader $expenseHeader, ExpenseHeaderFormService $expenseHeaderFormService, $_format = 'html'): Response
    {
        $expenseHeaderFormService->initialize($expenseHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ExpenseHeaderType::class, $expenseHeader);
        $form->handleRequest($request);
        $expenseHeaderFormService->finalize($expenseHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $expenseHeaderFormService->save($expenseHeader);

            return $this->redirectToRoute('app_accounting_expense_header_show', ['id' => $expenseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("accounting/expense_header/edit.{$_format}.twig", [
            'expenseHeader' => $expenseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_expense_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_EXPENSE_EDIT')]
    public function delete(Request $request, ExpenseHeader $expenseHeader, ExpenseHeaderRepository $expenseHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expenseHeader->getId(), $request->request->get('_token'))) {
            $expenseHeaderRepository->remove($expenseHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_expense_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
