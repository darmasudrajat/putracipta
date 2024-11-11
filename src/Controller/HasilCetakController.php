<?php

namespace App\Controller;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\HasilCetak;
use App\Form\HasilCetakType;
use App\Repository\HasilCetakRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Form\Purchase\PurchaseOrderHeaderType;
use App\Grid\Purchase\PurchaseOrderHeaderGridType;
use App\Repository\Purchase\PurchaseOrderHeaderRepository;
use App\Repository\Purchase\PurchaseRequestDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\Admin\LiteralConfigRepository;




    #[Route('/hasil/cetak')]
    class HasilCetakController extends AbstractController
{
    #[Route('/_list', name: 'app_hasil_cetak__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_HASIL_PRODUKSI_ADD') or is_granted('ROLE_HASIL_PRODUKSI_EDIT') or is_granted('ROLE_HASIL_PRODUKSI_VIEW')")]
    // public function _list(Request $request, CustomerRepository $customerRepository): Response
    // {
    //     $criteria = new DataCriteria();
    //     $form = $this->createForm(CustomerGridType::class, $criteria);
    //     $form->handleRequest($request);

    //     list($count, $customers) = $customerRepository->fetchData($criteria);

    //     return $this->renderForm("master/customer/_list.html.twig", [
    //         'form' => $form,
    //         'count' => $count,
    //         'customers' => $customers,
    //     ]);
    // }

    // public function _list(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    // {
    //     $criteria = new DataCriteria();
    //     $criteria->setSort([
    //         'transactionDate' => SortDescending::class,
    //     ]);
    //     $form = $this->createForm(PurchaseOrderHeaderGridType::class, $criteria);
    //     $form->handleRequest($request);

    //     list($count, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
    //         if (isset($request->request->get('purchase_order_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_order_header_grid')['sort']['supplier:company'])) {
    //             $qb->innerJoin("{$alias}.supplier", 's');
    //             $add['filter']($qb, 's', 'company', $request->request->get('purchase_order_header_grid')['filter']['supplier:company']);
    //             $add['sort']($qb, 's', 'company', $request->request->get('purchase_order_header_grid')['sort']['supplier:company']);
    //         }
    //     });

    //     return $this->renderForm("purchase/purchase_order_header/_list.html.twig", [
    //         'form' => $form,
    //         'count' => $count,
    //         'purchaseOrderHeaders' => $purchaseOrderHeaders,
    //     ]);
    // }
    

    #[Route('/', name: 'app_hasil_cetak_index', methods: ['GET'])]
    public function index(HasilCetakRepository $hasilCetakRepository): Response
    {
        return $this->render('hasil_cetak/index.html.twig', [
            'hasil_cetaks' => $hasilCetakRepository->findAll(),
            // 'hasil_cetaks' => $hasilCetakRepository->where('nomo', 22),
        ]);
    }

    #[Route('/new', name: 'app_hasil_cetak_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hasilCetak = new HasilCetak();
        $form = $this->createForm(HasilCetakType::class, $hasilCetak);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hasilCetak);
            $entityManager->flush();

            return $this->redirectToRoute('app_hasil_cetak_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hasil_cetak/new.html.twig', [
            'hasil_cetak' => $hasilCetak,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hasil_cetak_show', methods: ['GET'])]
    public function show(HasilCetak $hasilCetak): Response
    {
        return $this->render('hasil_cetak/show.html.twig', [
            'hasil_cetak' => $hasilCetak,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hasil_cetak_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HasilCetak $hasilCetak, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HasilCetakType::class, $hasilCetak);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hasil_cetak_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hasil_cetak/edit.html.twig', [
            'hasil_cetak' => $hasilCetak,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hasil_cetak_delete', methods: ['POST'])]
    public function delete(Request $request, HasilCetak $hasilCetak, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hasilCetak->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hasilCetak);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hasil_cetak_index', [], Response::HTTP_SEE_OTHER);
    }
}
  