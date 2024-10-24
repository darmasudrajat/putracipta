<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Accounting\DepositHeader;
use App\Form\Accounting\DepositHeaderType;
use App\Grid\Accounting\DepositHeaderGridType;
use App\Repository\Accounting\DepositHeaderRepository;
use App\Service\Accounting\DepositHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/deposit_header')]
class DepositHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_deposit_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DEPOSIT_ADD') or is_granted('ROLE_DEPOSIT_EDIT') or is_granted('ROLE_DEPOSIT_VIEW')")]
    public function _list(Request $request, DepositHeaderRepository $depositHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(DepositHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $depositHeaders) = $depositHeaderRepository->fetchData($criteria);

        return $this->renderForm("accounting/deposit_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'depositHeaders' => $depositHeaders,
        ]);
    }

    #[Route('/', name: 'app_accounting_deposit_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_DEPOSIT_ADD') or is_granted('ROLE_DEPOSIT_EDIT') or is_granted('ROLE_DEPOSIT_VIEW')")]
    public function index(): Response
    {
        return $this->render("accounting/deposit_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_accounting_deposit_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DEPOSIT_ADD')]
    public function new(Request $request, DepositHeaderFormService $depositHeaderFormService, $_format = 'html'): Response
    {
        $depositHeader = new DepositHeader();
        $depositHeaderFormService->initialize($depositHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(DepositHeaderType::class, $depositHeader);
        $form->handleRequest($request);
        $depositHeaderFormService->finalize($depositHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $depositHeaderFormService->save($depositHeader);

            return $this->redirectToRoute('app_accounting_deposit_header_show', ['id' => $depositHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("accounting/deposit_header/new.{$_format}.twig", [
            'depositHeader' => $depositHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_deposit_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_DEPOSIT_ADD') or is_granted('ROLE_DEPOSIT_EDIT') or is_granted('ROLE_DEPOSIT_VIEW')")]
    public function show(DepositHeader $depositHeader): Response
    {
        return $this->render('accounting/deposit_header/show.html.twig', [
            'depositHeader' => $depositHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_accounting_deposit_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DEPOSIT_EDIT')]
    public function edit(Request $request, DepositHeader $depositHeader, DepositHeaderFormService $depositHeaderFormService, $_format = 'html'): Response
    {
        $depositHeaderFormService->initialize($depositHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(DepositHeaderType::class, $depositHeader);
        $form->handleRequest($request);
        $depositHeaderFormService->finalize($depositHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $depositHeaderFormService->save($depositHeader);

            return $this->redirectToRoute('app_accounting_deposit_header_show', ['id' => $depositHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("accounting/deposit_header/edit.{$_format}.twig", [
            'depositHeader' => $depositHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_deposit_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DEPOSIT_EDIT')]
    public function delete(Request $request, DepositHeader $depositHeader, DepositHeaderRepository $depositHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $depositHeader->getId(), $request->request->get('_token'))) {
            $depositHeaderRepository->remove($depositHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_deposit_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
