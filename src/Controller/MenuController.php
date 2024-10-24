<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/menu')]
class MenuController extends AbstractController
{
    #[Route('/report', name: 'app_menu_report', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function report(): Response
    {
        return $this->render('menu/report.html.twig');
    }

    #[Route('/master', name: 'app_menu_master', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function master(): Response
    {
        return $this->render('menu/master.html.twig');
    }
}
