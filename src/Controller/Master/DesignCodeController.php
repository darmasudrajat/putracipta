<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DesignCode;
use App\Form\Master\DesignCodeType;
use App\Grid\Master\DesignCodeGridType;
use App\Service\Master\DesignCodeFormService;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Master\WorkOrderDistributionRepository;
use App\Repository\Master\WorkOrderCheckSheetRepository;
use App\Repository\Master\WorkOrderProcessRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/design_code')]
class DesignCodeController extends AbstractController
{
    #[Route('/_design_code_list', name: 'app_master_design_code__design_code_list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DESIGN_CODE_ADD') or is_granted('ROLE_DESIGN_CODE_EDIT') or is_granted('ROLE_DESIGN_CODE_VIEW')")]
    public function _designCodeList(Request $request, DesignCodeRepository $designCodeRepository): Response
    {
        $lastDesignCodes = $designCodeRepository->findBy(['customer' => $request->request->get('design_code')['customer'], 'status' => 'fa', 'isInactive' => false], ['id' => 'DESC'], 5, 0);

        return $this->render("master/design_code/_design_code_list.html.twig", [
            'lastDesignCodes' => $lastDesignCodes,
        ]);
    }

    #[Route('/_list', name: 'app_master_design_code__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DESIGN_CODE_ADD') or is_granted('ROLE_DESIGN_CODE_EDIT') or is_granted('ROLE_DESIGN_CODE_VIEW')")]
    public function _list(Request $request, DesignCodeRepository $designCodeRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'createdTransactionDateTime' => SortDescending::class,
            'name' => SortAscending::class,
        ]);
        $form = $this->createForm(DesignCodeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $designCodes) = $designCodeRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('design_code_grid')['filter']['customer:company']) && isset($request->request->get('design_code_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('design_code_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('design_code_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("master/design_code/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'designCodes' => $designCodes,
        ]);
    }

    #[Route('/', name: 'app_master_design_code_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_DESIGN_CODE_ADD') or is_granted('ROLE_DESIGN_CODE_EDIT') or is_granted('ROLE_DESIGN_CODE_VIEW')")]
    public function index(): Response
    {
        return $this->render("master/design_code/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_master_design_code_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DESIGN_CODE_ADD')]
    public function new(Request $request, DesignCodeFormService $designCodeFormService, WorkOrderProcessRepository $workOrderProcessRepository, WorkOrderDistributionRepository $workOrderDistributionRepository, WorkOrderCheckSheetRepository $workOrderCheckSheetRepository, $_format = 'html'): Response
    {
        $designCode = new DesignCode();
        $designCodeFormService->initialize($designCode, ['datetime' => new \DateTime(), 'user' => $this->getUser(), 'sourceDesignCode' => null]);
        $form = $this->createForm(DesignCodeType::class, $designCode);
        $form->handleRequest($request);
        $designCodeFormService->finalize($designCode);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $designCodeFormService->save($designCode, ['sourceDesignCode' => null]);

            return $this->redirectToRoute('app_master_design_code_show', ['id' => $designCode->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("master/design_code/new.{$_format}.twig", [
            'designCode' => $designCode,
            'form' => $form,
            'workOrderCheckSheets' => $workOrderCheckSheetRepository->findAll(),
            'workOrderDistributions' => $workOrderDistributionRepository->findAll(),
            'workOrderProcesses' => $workOrderProcessRepository->findAll(),
            'lastDesignCodes' => [],
        ]);
    }

    #[Route('/{id}', name: 'app_master_design_code_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_DESIGN_CODE_ADD') or is_granted('ROLE_DESIGN_CODE_EDIT') or is_granted('ROLE_DESIGN_CODE_VIEW')")]
    public function show(DesignCode $designCode): Response
    {
        return $this->render('master/design_code/show.html.twig', [
            'designCode' => $designCode,
        ]);
    }

    #[Route('/{source_id}/new_repeat.{_format}', name: 'app_master_design_code_new_repeat', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DESIGN_CODE_ADD')]
    public function newRepeat(Request $request, DesignCodeRepository $designCodeRepository, DesignCodeFormService $designCodeFormService, WorkOrderProcessRepository $workOrderProcessRepository, WorkOrderDistributionRepository $workOrderDistributionRepository, WorkOrderCheckSheetRepository $workOrderCheckSheetRepository, $_format = 'html'): Response
    {
        $sourceDesignCode = $designCodeRepository->find($request->attributes->getInt('source_id'));
        $designCode = $designCodeFormService->copyFrom($sourceDesignCode);
        $designCodeFormService->initialize($designCode, ['datetime' => new \DateTime(), 'user' => $this->getUser(), 'sourceDesignCode' => $sourceDesignCode]);
        $form = $this->createForm(DesignCodeType::class, $designCode);
        $form->handleRequest($request);
        $designCodeFormService->finalize($designCode);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $designCodeFormService->save($designCode, ['sourceDesignCode' => $sourceDesignCode]);

            return $this->redirectToRoute('app_master_design_code_show', ['id' => $designCode->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("master/design_code/new_repeat.{$_format}.twig", [
            'designCode' => $designCode,
            'form' => $form,
            'workOrderCheckSheets' => $workOrderCheckSheetRepository->findAll(),
            'workOrderDistributions' => $workOrderDistributionRepository->findAll(),
            'workOrderProcesses' => $workOrderProcessRepository->findAll(),
            'lastDesignCodes' => $designCodeRepository->findBy(['customer' => $designCode->getCustomer()], ['id' => 'DESC'], 5, 0),
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_master_design_code_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DESIGN_CODE_EDIT')]
    public function edit(Request $request, DesignCode $designCode, DesignCodeRepository $designCodeRepository, DesignCodeFormService $designCodeFormService, WorkOrderProcessRepository $workOrderProcessRepository, WorkOrderDistributionRepository $workOrderDistributionRepository, WorkOrderCheckSheetRepository $workOrderCheckSheetRepository, $_format = 'html'): Response
    {
        $designCodeFormService->initialize($designCode, ['datetime' => new \DateTime(), 'user' => $this->getUser(), 'sourceDesignCode' => null]);
        $form = $this->createForm(DesignCodeType::class, $designCode);
        $form->handleRequest($request);
        $designCodeFormService->finalize($designCode);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $designCodeFormService->save($designCode, ['sourceDesignCode' => null]);

            return $this->redirectToRoute('app_master_design_code_show', ['id' => $designCode->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("master/design_code/edit.{$_format}.twig", [
            'designCode' => $designCode,
            'form' => $form,
            'workOrderCheckSheets' => $workOrderCheckSheetRepository->findAll(),
            'workOrderDistributions' => $workOrderDistributionRepository->findAll(),
            'workOrderProcesses' => $workOrderProcessRepository->findAll(),
            'lastDesignCodes' => $designCodeRepository->findBy(['customer' => $designCode->getCustomer()], ['id' => 'DESC'], 5, 0),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_design_code_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DESIGN_CODE_EDIT')]
    public function delete(Request $request, DesignCode $designCode, DesignCodeRepository $designCodeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $designCode->getId(), $request->request->get('_token'))) {
            $designCodeRepository->remove($designCode, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_design_code_index', [], Response::HTTP_SEE_OTHER);
    }
}
