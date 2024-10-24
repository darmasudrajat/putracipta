<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Customer;
use App\Form\Master\CustomerType;
use App\Grid\Master\CustomerGridType;
use App\Repository\Master\CustomerRepository;
use App\Service\Master\CustomerFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/customer')]
class CustomerController extends AbstractController
{
    #[Route('/_list', name: 'app_master_customer__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CUSTOMER_ADD') or is_granted('ROLE_CUSTOMER_EDIT') or is_granted('ROLE_CUSTOMER_VIEW')")]
    public function _list(Request $request, CustomerRepository $customerRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(CustomerGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $customers) = $customerRepository->fetchData($criteria);

        return $this->renderForm("master/customer/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'customers' => $customers,
        ]);
    }

    #[Route('/', name: 'app_master_customer_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_CUSTOMER_ADD') or is_granted('ROLE_CUSTOMER_EDIT') or is_granted('ROLE_CUSTOMER_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/customer/index.html.twig");
    }

    #[Route('/new', name: 'app_master_customer_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CUSTOMER_ADD')]
    public function new(Request $request, CustomerFormService $customerFormService): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $customerFormService->save($customer);

            return $this->redirectToRoute('app_master_customer_show', ['id' => $customer->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_customer_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_CUSTOMER_ADD') or is_granted('ROLE_CUSTOMER_EDIT') or is_granted('ROLE_CUSTOMER_VIEW')")]
    public function show(Customer $customer): Response
    {
        return $this->render('master/customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_customer_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CUSTOMER_EDIT')]
    public function edit(Request $request, Customer $customer, CustomerFormService $customerFormService): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $customerFormService->save($customer);

            return $this->redirectToRoute('app_master_customer_show', ['id' => $customer->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_customer_delete', methods: ['POST'])]
    #[IsGranted('ROLE_CUSTOMER_EDIT')]
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->request->get('_token'))) {
            $customerRepository->remove($customer, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
