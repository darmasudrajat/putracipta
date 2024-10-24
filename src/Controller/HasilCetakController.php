<?php

namespace App\Controller;

use App\Entity\HasilCetak;
use App\Form\HasilCetakType;
use App\Repository\HasilCetakRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;





    #[Route('/hasil/cetak')]
    class HasilCetakController extends AbstractController
{
    #[Route('/', name: 'app_hasil_cetak_index', methods: ['GET'])]
    public function index(HasilCetakRepository $hasilCetakRepository): Response
    {
        return $this->render('hasil_cetak/index.html.twig', [
            'hasil_cetaks' => $hasilCetakRepository->findAll(),
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
    