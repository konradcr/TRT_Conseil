<?php

namespace App\Controller;

use App\Repository\ConsultantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function adminDashboard(ConsultantRepository $consultantRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'consultants' => $consultantRepository->findAll(),
        ]);
    }
}
