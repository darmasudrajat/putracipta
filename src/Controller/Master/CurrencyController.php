<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Currency;
use App\Form\Master\CurrencyType;
use App\Grid\Master\CurrencyGridType;
use App\Repository\Master\CurrencyRepository;
use App\Service\Master\CurrencyFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/currency')]
class CurrencyController extends AbstractController
{
    #[Route('/_list', name: 'app_master_currency__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, CurrencyRepository $currencyRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(CurrencyGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $currencies) = $currencyRepository->fetchData($criteria);

        return $this->renderForm("master/currency/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'currencies' => $currencies,
        ]);
    }

    #[Route('/', name: 'app_master_currency_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/currency/index.html.twig");
    }

    #[Route('/new', name: 'app_master_currency_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, CurrencyFormService $currencyFormService): Response
    {
        $currency = new Currency();
        $form = $this->createForm(CurrencyType::class, $currency);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $currencyFormService->save($currency);

            return $this->redirectToRoute('app_master_currency_show', ['id' => $currency->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/currency/new.html.twig', [
            'currency' => $currency,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_currency_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Currency $currency): Response
    {
        return $this->render('master/currency/show.html.twig', [
            'currency' => $currency,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_currency_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Currency $currency, CurrencyFormService $currencyFormService): Response
    {
        $form = $this->createForm(CurrencyType::class, $currency);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $currencyFormService->save($currency);

            return $this->redirectToRoute('app_master_currency_show', ['id' => $currency->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/currency/edit.html.twig', [
            'currency' => $currency,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_currency_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Currency $currency, CurrencyRepository $currencyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $currency->getId(), $request->request->get('_token'))) {
            $currencyRepository->remove($currency, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_currency_index', [], Response::HTTP_SEE_OTHER);
    }
}
