<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\ProductPrototype;
use App\Form\Production\ProductPrototypeType;
use App\Grid\Production\ProductPrototypeGridType;
use App\Repository\Production\ProductDevelopmentRepository;
use App\Repository\Production\ProductPrototypeRepository;
use App\Service\Production\ProductPrototypeFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/product_prototype')]
class ProductPrototypeController extends AbstractController
{
    #[Route('/_list', name: 'app_production_product_prototype__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function _list(Request $request, ProductPrototypeRepository $productPrototypeRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(ProductPrototypeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $productPrototypes) = $productPrototypeRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('product_prototype_grid')['filter']['customer:company']) && isset($request->request->get('product_prototype_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 'c');
                $add['filter']($qb, 'c', 'company', $request->request->get('product_prototype_grid')['filter']['customer:company']);
                $add['sort']($qb, 'c', 'company', $request->request->get('product_prototype_grid')['sort']['customer:company']);
            }
            if (isset($request->request->get('product_prototype_grid')['filter']['designCode:codeNumber']) && isset($request->request->get('product_prototype_grid')['sort']['designCode:codeNumber'])) {
                $qb->innerJoin("{$alias}.designCode", 'd');
                $add['filter']($qb, 'd', 'code', $request->request->get('product_prototype_grid')['filter']['designCode:code']);
                $add['sort']($qb, 'd', 'code', $request->request->get('product_prototype_grid')['sort']['designCode:code']);
                $add['filter']($qb, 'd', 'variant', $request->request->get('product_prototype_grid')['filter']['designCode:variant']);
                $add['sort']($qb, 'd', 'variant', $request->request->get('product_prototype_grid')['sort']['designCode:variant']);
                $add['filter']($qb, 'd', 'version', $request->request->get('product_prototype_grid')['filter']['designCode:version']);
                $add['sort']($qb, 'd', 'version', $request->request->get('product_prototype_grid')['sort']['designCode:version']);
            }
            if (isset($request->request->get('product_prototype_grid')['filter']['paper:name']) && isset($request->request->get('product_prototype_grid')['sort']['paper:name'])) {
                $qb->innerJoin("{$alias}.paper", 'p');
                $add['filter']($qb, 'p', 'name', $request->request->get('product_prototype_grid')['filter']['paper:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('product_prototype_grid')['sort']['paper:name']);
            }
        });

        return $this->renderForm("production/product_prototype/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'productPrototypes' => $productPrototypes,
        ]);
    }

    #[Route('/', name: 'app_production_product_prototype_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function index(): Response
    {
        return $this->render("production/product_prototype/index.html.twig");
    }

    #[Route('/_head', name: 'app_production_product_prototype__head', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function _head(Request $request, ProductPrototypeRepository $productPrototypeRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $productPrototypes) = $productPrototypeRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
            $qb->innerJoin("{$alias}.employee", 'm');
            $qb->andWhere("m.user = :user");
            $qb->setParameter('user', $this->getUser());
        });

        return $this->renderForm("production/product_prototype/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'productPrototypes' => $productPrototypes,
        ]);
    }

    #[Route('/head', name: 'app_production_product_prototype_head', methods: ['GET'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function head(): Response
    {
        return $this->render("production/product_prototype/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_production_product_prototype_read', methods: ['POST'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function read(Request $request, ProductPrototype $productPrototype, ProductPrototypeRepository $productPrototypeRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $productPrototype->getId(), $request->request->get('_token'))) {
            $productPrototype->setIsRead(true);
            $productPrototypeRepository->add($productPrototype, true);
        }

        return $this->redirectToRoute('app_production_product_prototype_show', ['id' => $productPrototype->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_production_product_prototype_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_NEW_PRODUCT_ADD')]
    public function new(Request $request, ProductPrototypeFormService $productPrototypeFormService, $_format = 'html'): Response
    {
        $productPrototype = new ProductPrototype();
        $productPrototypeFormService->initialize($productPrototype, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ProductPrototypeType::class, $productPrototype);
        $form->handleRequest($request);
        $productPrototypeFormService->finalize($productPrototype, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $productPrototypeFormService->save($productPrototype);
            $productPrototypeFormService->uploadFile($productPrototype, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/product-prototype');

            return $this->redirectToRoute('app_production_product_prototype_show', ['id' => $productPrototype->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/product_prototype/new.{$_format}.twig", [
            'productPrototype' => $productPrototype,
            'form' => $form,
            'transactionFileExists' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_production_product_prototype_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function show(ProductPrototype $productPrototype, ProductDevelopmentRepository $productDevelopmentRepository): Response
    {
        $productDevelopment = $productDevelopmentRepository->findBy(['productPrototype' => $productPrototype->getId()], ['id' => 'DESC'], 1, 0);
        
        return $this->render('production/product_prototype/show.html.twig', [
            'productPrototype' => $productPrototype,
            'layoutImage' => empty($productDevelopment) ? false : $productDevelopment[0],
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_product_prototype_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_NEW_PRODUCT_EDIT')]
    public function edit(Request $request, ProductPrototype $productPrototype, ProductPrototypeFormService $productPrototypeFormService, $_format = 'html'): Response
    {
        $productPrototypeFormService->initialize($productPrototype, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ProductPrototypeType::class, $productPrototype);
        $form->handleRequest($request);
        $productPrototypeFormService->finalize($productPrototype, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $productPrototypeFormService->save($productPrototype);
            $productPrototypeFormService->uploadFile($productPrototype, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/product-prototype');

            return $this->redirectToRoute('app_production_product_prototype_show', ['id' => $productPrototype->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/product_prototype/edit.{$_format}.twig", [
            'productPrototype' => $productPrototype,
            'form' => $form,
            'transactionFileExists' => file_exists($this->getParameter('kernel.project_dir') . '/public/uploads/product-prototype/' . $productPrototype->getId() . '.' . $productPrototype->getTransactionFileExtension()),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_product_prototype_delete', methods: ['POST'])]
    #[IsGranted('ROLE_NEW_PRODUCT_EDIT')]
    public function delete(Request $request, ProductPrototype $productPrototype, ProductPrototypeRepository $productPrototypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $productPrototype->getId(), $request->request->get('_token'))) {
            $productPrototypeRepository->remove($productPrototype, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_product_prototype_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/memo', name: 'app_production_product_prototype_memo', methods: ['GET'])]
    #[Security("is_granted('ROLE_NEW_PRODUCT_ADD') or is_granted('ROLE_NEW_PRODUCT_EDIT') or is_granted('ROLE_NEW_PRODUCT_VIEW')")]
    public function memo(ProductPrototype $productPrototype): Response
    {
        $fileName = 'form_produk_baru.pdf';
        $htmlView = $this->renderView('production/product_prototype/memo.html.twig', [
            'productPrototype' => $productPrototype,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
//            fn($html, $chrootDir) => preg_replace('/<img id="upload"(.+)src=".+">/', '<img id="upload"\1src="' . $chrootDir . 'uploads/product-development/' . $productPrototype->getId() . '.' . $productPrototype->getTransactionFileExtension() . '">', $html),
        ]);
    }
}
