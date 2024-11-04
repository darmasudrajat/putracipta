<?php

namespace App\Controller;

use App\Entity\Hasilpond;
use App\Form\HasilpondType;
use App\Repository\HasilpondRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hasilpond')]
class HasilpondController extends AbstractController
{
    #[Route('/', name: 'app_hasilpond_index', methods: ['GET'])]
    public function index(HasilpondRepository $hasilpondRepository): Response
    {
        return $this->render('hasilpond/index.html.twig', [
            'hasilponds' => $hasilpondRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hasilpond_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hasilpond = new Hasilpond();
        $form = $this->createForm(HasilpondType::class, $hasilpond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hasilpond);
            $entityManager->flush();

            return $this->redirectToRoute('app_hasilpond_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hasilpond/new.html.twig', [
            'hasilpond' => $hasilpond,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hasilpond_show', methods: ['GET'])]
    public function show(Hasilpond $hasilpond): Response
    {
        return $this->render('hasilpond/show.html.twig', [
            'hasilpond' => $hasilpond,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hasilpond_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hasilpond $hasilpond, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HasilpondType::class, $hasilpond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hasilpond_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hasilpond/edit.html.twig', [
            'hasilpond' => $hasilpond,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hasilpond_delete', methods: ['POST'])]
    public function delete(Request $request, Hasilpond $hasilpond, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hasilpond->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hasilpond);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hasilpond_index', [], Response::HTTP_SEE_OTHER);
    }
}
