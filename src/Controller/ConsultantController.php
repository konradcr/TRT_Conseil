<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantController extends AbstractController
{
    #[Route('/consultant', name: 'app_consultant')]
    public function index(): Response
    {
        return $this->render('consultant/index.html.twig', [
            'controller_name' => 'ConsultantController',
        ]);
    }

    #[Route('/consultant-approve-user', name: 'app_consultant_approve_user')]
    public function approveUser(): Response
    {
        return $this->render('consultant/index.html.twig', [
            'controller_name' => 'ConsultantController',
        ]);
    }
}