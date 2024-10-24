<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\ProductDevelopment;
use App\Form\Production\ProductDevelopmentType;
use App\Grid\Production\ProductDevelopmentGridType;
use App\Repository\Production\ProductDevelopmentRepository;
use App\Service\Production\ProductDevelopmentFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/product_development')]
class ProductDevelopmentController extends AbstractController
{
    #[Route('/_list', name: 'app_production_product_development__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DEVELOPMENT_PRODUCT_ADD') or is_granted('ROLE_DEVELOPMENT_PRODUCT_EDIT') or is_granted('ROLE_DEVELOPMENT_PRODUCT_VIEW')")]
    public function _list(Request $request, ProductDevelopmentRepository $productDevelopmentRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(ProductDevelopmentGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $productDevelopments) = $productDevelopmentRepository->fetchData($criteria);

        return $this->renderForm("production/product_development/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'productDevelopments' => $productDevelopments,
        ]);
    }

    #[Route('/', name: 'app_production_product_development_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_DEVELOPMENT_PRODUCT_ADD') or is_granted('ROLE_DEVELOPMENT_PRODUCT_EDIT') or is_granted('ROLE_DEVELOPMENT_PRODUCT_VIEW')")]
    public function index(): Response
    {
        return $this->render("production/product_development/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_product_development_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DEVELOPMENT_PRODUCT_ADD')]
    public function new(Request $request, ProductDevelopmentFormService $productDevelopmentFormService, $_format = 'html'): Response
    {
        $productDevelopment = new ProductDevelopment();
        $productDevelopmentFormService->initialize($productDevelopment, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ProductDevelopmentType::class, $productDevelopment);
        $form->handleRequest($request);
        $productDevelopmentFormService->finalize($productDevelopment, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $productDevelopmentFormService->save($productDevelopment);
            $productDevelopmentFormService->uploadFile($productDevelopment, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/product-development');

            return $this->redirectToRoute('app_production_product_development_show', ['id' => $productDevelopment->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/product_development/new.{$_format}.twig", [
            'productDevelopment' => $productDevelopment,
            'form' => $form,
            'transactionFileExists' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_production_product_development_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_DEVELOPMENT_PRODUCT_ADD') or is_granted('ROLE_DEVELOPMENT_PRODUCT_EDIT') or is_granted('ROLE_DEVELOPMENT_PRODUCT_VIEW')")]
    public function show(ProductDevelopment $productDevelopment): Response
    {
        return $this->render('production/product_development/show.html.twig', [
            'productDevelopment' => $productDevelopment,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_product_development_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DEVELOPMENT_PRODUCT_EDIT')]
    public function edit(Request $request, ProductDevelopment $productDevelopment, ProductDevelopmentFormService $productDevelopmentFormService, $_format = 'html'): Response
    {
        $productDevelopmentFormService->initialize($productDevelopment, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ProductDevelopmentType::class, $productDevelopment);
        $form->handleRequest($request);
        $productDevelopmentFormService->finalize($productDevelopment, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $productDevelopmentFormService->save($productDevelopment);
            $productDevelopmentFormService->uploadFile($productDevelopment, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/product-development');

            return $this->redirectToRoute('app_production_product_development_show', ['id' => $productDevelopment->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/product_development/edit.{$_format}.twig", [
            'productDevelopment' => $productDevelopment,
            'form' => $form,
            'transactionFileExists' => file_exists($this->getParameter('kernel.project_dir') . '/public/uploads/product-development/' . $productDevelopment->getId() . '.' . $productDevelopment->getTransactionFileExtension()),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_product_development_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DEVELOPMENT_PRODUCT_EDIT')]
    public function delete(Request $request, ProductDevelopment $productDevelopment, ProductDevelopmentRepository $productDevelopmentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $productDevelopment->getId(), $request->request->get('_token'))) {
            $productDevelopmentRepository->remove($productDevelopment, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_product_development_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/memo', name: 'app_production_product_development_memo', methods: ['GET'])]
    #[Security("is_granted('ROLE_DEVELOPMENT_PRODUCT_ADD') or is_granted('ROLE_DEVELOPMENT_PRODUCT_EDIT') or is_granted('ROLE_DEVELOPMENT_PRODUCT_VIEW')")]
    public function memo(ProductDevelopment $productDevelopment): Response
    {
        $fileName = 'form_produk_baru.pdf';
        $htmlView = $this->renderView('production/product_development/memo.html.twig', [
            'productDevelopment' => $productDevelopment,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="upload"(.+)src=".+">/', '<img id="upload"\1src="' . $chrootDir . 'uploads/product-development/' . $productDevelopment->getFileName() . '">', $html),
        ]);
    }
}
