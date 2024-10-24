<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\MasterOrderHeader;
use App\Form\Production\MasterOrderHeaderType;
use App\Grid\Production\MasterOrderHeaderGridType;
use App\Repository\Master\WorkOrderCheckSheetRepository;
use App\Repository\Master\WorkOrderDistributionRepository;
use App\Repository\Master\WorkOrderProcessRepository;
use App\Repository\Production\MasterOrderHeaderRepository;
use App\Service\Production\MasterOrderHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/master_order_header')]
class MasterOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_master_order_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_MASTER_ORDER_ADD') or is_granted('ROLE_MASTER_ORDER_EDIT') or is_granted('ROLE_MASTER_ORDER_VIEW')")]
    public function _list(Request $request, MasterOrderHeaderRepository $masterOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(MasterOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $masterOrderHeaders) = $masterOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('master_order_header_grid')['filter']['customer:company']) && isset($request->request->get('master_order_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 'c');
                $add['filter']($qb, 'c', 'company', $request->request->get('master_order_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 'c', 'company', $request->request->get('master_order_header_grid')['sort']['customer:company']);
            }
            if (isset($request->request->get('master_order_header_grid')['filter']['designCode:code']) && isset($request->request->get('master_order_header_grid')['sort']['designCode:code'])) {
                $qb->innerJoin("{$alias}.designCode", 'd');
                $add['filter']($qb, 'd', 'code', $request->request->get('master_order_header_grid')['filter']['designCode:code']);
                $add['sort']($qb, 'd', 'code', $request->request->get('master_order_header_grid')['sort']['designCode:code']);
            }
            if (isset($request->request->get('master_order_header_grid')['filter']['designCode:variant']) && isset($request->request->get('master_order_header_grid')['sort']['designCode:variant'])) {
                $qb->innerJoin("{$alias}.designCode", 'i');
                $add['filter']($qb, 'i', 'variant', $request->request->get('master_order_header_grid')['filter']['designCode:variant']);
                $add['sort']($qb, 'i', 'variant', $request->request->get('master_order_header_grid')['sort']['designCode:variant']);
            }
            if (isset($request->request->get('master_order_header_grid')['filter']['designCode:version']) && isset($request->request->get('master_order_header_grid')['sort']['designCode:version'])) {
                $qb->innerJoin("{$alias}.designCode", 's');
                $add['filter']($qb, 's', 'version', $request->request->get('master_order_header_grid')['filter']['designCode:version']);
                $add['sort']($qb, 's', 'version', $request->request->get('master_order_header_grid')['sort']['designCode:version']);
            }
        });

        return $this->renderForm("production/master_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'masterOrderHeaders' => $masterOrderHeaders,
        ]);
    }

    #[Route('/', name: 'app_production_master_order_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_MASTER_ORDER_ADD') or is_granted('ROLE_MASTER_ORDER_EDIT') or is_granted('ROLE_MASTER_ORDER_VIEW')")]
    public function index(): Response
    {
        return $this->render("production/master_order_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_master_order_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MASTER_ORDER_ADD')]
    public function new(Request $request, MasterOrderHeaderFormService $masterOrderHeaderFormService, WorkOrderProcessRepository $workOrderProcessRepository, WorkOrderCheckSheetRepository $workOrderCheckSheetRepository, WorkOrderDistributionRepository $workOrderDistributionRepository, $_format = 'html'): Response
    {
        $masterOrderHeader = new MasterOrderHeader();
        $masterOrderHeaderFormService->initialize($masterOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MasterOrderHeaderType::class, $masterOrderHeader);
        $form->handleRequest($request);
        $masterOrderHeaderFormService->finalize($masterOrderHeader, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $masterOrderHeaderFormService->save($masterOrderHeader);
            $masterOrderHeaderFormService->uploadFile($masterOrderHeader, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/master-order');

            return $this->redirectToRoute('app_production_master_order_header_show', ['id' => $masterOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/master_order_header/new.{$_format}.twig", [
            'masterOrderHeader' => $masterOrderHeader,
            'form' => $form,
            'transactionFileExists' => false,
            'workOrderProcesses' => $workOrderProcessRepository->findAll(),
            'workOrderCheckSheets' => $workOrderCheckSheetRepository->findAll(),
            'workOrderDistributions' => $workOrderDistributionRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_production_master_order_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_MASTER_ORDER_ADD') or is_granted('ROLE_MASTER_ORDER_EDIT') or is_granted('ROLE_MASTER_ORDER_VIEW')")]
    public function show(MasterOrderHeader $masterOrderHeader): Response
    {
        return $this->render('production/master_order_header/show.html.twig', [
            'masterOrderHeader' => $masterOrderHeader,
        ]);
    }

    #[Route('/{source_id}/new_repeat.{_format}', name: 'app_production_master_order_header_new_repeat', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MASTER_ORDER_ADD')]
    public function newRepeat(Request $request, MasterOrderHeaderRepository $masterOrderHeaderRepository, MasterOrderHeaderFormService $masterOrderHeaderFormService, WorkOrderProcessRepository $workOrderProcessRepository, WorkOrderCheckSheetRepository $workOrderCheckSheetRepository, WorkOrderDistributionRepository $workOrderDistributionRepository, $_format = 'html'): Response
    {
        $sourceMasterOrderHeader = $masterOrderHeaderRepository->find($request->attributes->getInt('source_id'));
        $masterOrderHeader = $masterOrderHeaderFormService->copyFrom($sourceMasterOrderHeader);
        $masterOrderHeaderFormService->initialize($masterOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MasterOrderHeaderType::class, $masterOrderHeader);
        $form->handleRequest($request);
        $masterOrderHeaderFormService->finalize($masterOrderHeader, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $masterOrderHeaderFormService->save($masterOrderHeader);
            $masterOrderHeaderFormService->uploadFile($masterOrderHeader, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/sale-order');

            return $this->redirectToRoute('app_production_master_order_header_show', ['id' => $masterOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/master_order_header/new_repeat.{$_format}.twig", [
            'masterOrderHeader' => $masterOrderHeader,
            'form' => $form,
            'transactionFileExists' => false,
            'workOrderProcesses' => $workOrderProcessRepository->findAll(),
            'workOrderCheckSheets' => $workOrderCheckSheetRepository->findAll(),
            'workOrderDistributions' => $workOrderDistributionRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_master_order_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MASTER_ORDER_EDIT')]
    public function edit(Request $request, MasterOrderHeader $masterOrderHeader, MasterOrderHeaderFormService $masterOrderHeaderFormService, WorkOrderProcessRepository $workOrderProcessRepository, WorkOrderCheckSheetRepository $workOrderCheckSheetRepository, WorkOrderDistributionRepository $workOrderDistributionRepository, $_format = 'html'): Response
    {
        $masterOrderHeaderFormService->initialize($masterOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MasterOrderHeaderType::class, $masterOrderHeader);
        $form->handleRequest($request);
        $masterOrderHeaderFormService->finalize($masterOrderHeader, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $masterOrderHeaderFormService->save($masterOrderHeader);
            $masterOrderHeaderFormService->uploadFile($masterOrderHeader, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/master-order');

            return $this->redirectToRoute('app_production_master_order_header_show', ['id' => $masterOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/master_order_header/edit.{$_format}.twig", [
            'masterOrderHeader' => $masterOrderHeader,
            'form' => $form,
            'transactionFileExists' => file_exists($this->getParameter('kernel.project_dir') . '/public/uploads/master-order/' . $masterOrderHeader->getId() . '.' . $masterOrderHeader->getLayoutModelFileExtension()),
            'workOrderProcesses' => $workOrderProcessRepository->findAll(),
            'workOrderCheckSheets' => $workOrderCheckSheetRepository->findAll(),
            'workOrderDistributions' => $workOrderDistributionRepository->findAll(),
        ]);
    }

    #[Route('/{id}/memo_master_order', name: 'app_production_master_order_header_memo_master_order', methods: ['GET'])]
    #[Security("is_granted('ROLE_MASTER_ORDER_ADD') or is_granted('ROLE_MASTER_ORDER_EDIT') or is_granted('ROLE_MASTER_ORDER_VIEW')")]
    public function memoMasterOrder(MasterOrderHeader $masterOrderHeader): Response
    {
        $fileName = 'master_order.pdf';
        $htmlView = $this->renderView('production/master_order_header/memo_master_order.html.twig', [
            'masterOrderHeader' => $masterOrderHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="upload"(.+)src=".+">/', '<img id="upload"\1src="' . $chrootDir . 'uploads/master-order/' . $masterOrderHeader->getFileName() . '">', $html),
        ]);
    }
    
    #[Route('/{id}/memo_work_order', name: 'app_production_master_order_header_memo_work_order', methods: ['GET'])]
    #[Security("is_granted('ROLE_MASTER_ORDER_ADD') or is_granted('ROLE_MASTER_ORDER_EDIT') or is_granted('ROLE_MASTER_ORDER_VIEW')")]
    public function memoWorkOrder(MasterOrderHeader $masterOrderHeader): Response
    {
        $fileName = 'work_order.pdf';
        $htmlView = $this->renderView('production/master_order_header/memo_work_order.html.twig', [
            'masterOrderHeader' => $masterOrderHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="upload"(.+)src=".+">/', '<img id="upload"\1src="' . $chrootDir . 'uploads/master-order/' . $masterOrderHeader->getFileName() . '">', $html),
        ]);
    }
    
    #[Route('/{id}/{constant}/memo_distribution', name: 'app_production_master_order_header_memo_distribution', methods: ['GET'])]
    #[Security("is_granted('ROLE_MASTER_ORDER_ADD') or is_granted('ROLE_MASTER_ORDER_EDIT') or is_granted('ROLE_MASTER_ORDER_VIEW')")]
    public function memoDistribution(MasterOrderHeader $masterOrderHeader, string $constant): Response
    {
        return $this->render('production/master_order_header/memo_distribution.html.twig', [
            'masterOrderHeader' => $masterOrderHeader,
            'constant' => $constant,
        ]);
    }
    
    #[Route('/{id}/{constant}/memo_check_sheet', name: 'app_production_master_order_header_memo_check_sheet', methods: ['GET'])]
    #[Security("is_granted('ROLE_MASTER_ORDER_ADD') or is_granted('ROLE_MASTER_ORDER_EDIT') or is_granted('ROLE_MASTER_ORDER_VIEW')")]
    public function memoCheckSheet(MasterOrderHeader $masterOrderHeader, string $constant): Response
    {
        return $this->render('production/master_order_header/memo_check_sheet.html.twig', [
            'masterOrderHeader' => $masterOrderHeader,
            'constant' => $constant,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'app_production_master_order_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MASTER_ORDER_EDIT')]
    public function delete(Request $request, MasterOrderHeader $masterOrderHeader, MasterOrderHeaderRepository $masterOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $masterOrderHeader->getId(), $request->request->get('_token'))) {
            $masterOrderHeaderRepository->remove($masterOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_master_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
