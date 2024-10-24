<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Employee;
use App\Form\Master\EmployeeType;
use App\Grid\Master\EmployeeGridType;
use App\Repository\Master\EmployeeRepository;
use App\Service\Master\EmployeeFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/_list', name: 'app_master_employee__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_EMPLOYEE_ADD') or is_granted('ROLE_EMPLOYEE_EDIT') or is_granted('ROLE_EMPLOYEE_VIEW')")]
    public function _list(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'name' => SortAscending::class,
        ]);
        $form = $this->createForm(EmployeeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $employees) = $employeeRepository->fetchData($criteria);

        return $this->renderForm("master/employee/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'employees' => $employees,
        ]);
    }

    #[Route('/', name: 'app_master_employee_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_EMPLOYEE_ADD') or is_granted('ROLE_EMPLOYEE_EDIT') or is_granted('ROLE_EMPLOYEE_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/employee/index.html.twig");
    }

    #[Route('/new', name: 'app_master_employee_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EMPLOYEE_ADD')]
    public function new(Request $request, EmployeeFormService $employeeFormService): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $employeeFormService->save($employee);
            if ($request->request->has('submit_save')) {
                return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
            } else if ($request->request->has('submit_save_add_user')) {
                return $this->redirectToRoute('app_admin_user_new', ['referral_id' => $employee->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('master/employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_employee_show', methods: ['GET'])]
    #[Security("user === employee.getUser() or is_granted('ROLE_EMPLOYEE_ADD') or is_granted('ROLE_EMPLOYEE_EDIT') or is_granted('ROLE_EMPLOYEE_VIEW')")]
    public function show(Employee $employee): Response
    {
        return $this->render('master/employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_employee_edit', methods: ['GET', 'POST'])]
    #[Security("user === employee.getUser() or is_granted('ROLE_EMPLOYEE_EDIT')")]
    public function edit(Request $request, Employee $employee, EmployeeFormService $employeeFormService): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $employeeFormService->save($employee);

            return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_employee_delete', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE_EDIT')]
    public function delete(Request $request, Employee $employee, EmployeeRepository $employeeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $employee->getId(), $request->request->get('_token'))) {
            $employeeRepository->remove($employee, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_employee_index', [], Response::HTTP_SEE_OTHER);
    }
}
