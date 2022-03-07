<?php

namespace App\Controller;

use App\Form\EditRecruiterProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecruiterController extends AbstractController
{
    #[Route('/recruiter', name: 'app_recruiter')]
    public function recruiterDashboard(): Response
    {
        return $this->render('recruiter/index.html.twig', [
            'controller_name' => 'RecruiterController',
        ]);
    }

    #[Route('/recruiter/profile', name: 'app_recruiter_profile')]
    public function recruiterProfile(): Response
    {
        return $this->render('recruiter/profile.html.twig', [
        ]);
    }

    #[Route('/recruiter/profile/edit', name: 'app_recruiter_edit_profile')]
    public function editRecruiterProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recruiter = $this->getUser();
        $form = $this->createForm(EditRecruiterProfileType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($recruiter);
            $entityManager->flush();

            return $this->redirectToRoute('app_recruiter_profile');
        }

        return $this->render('recruiter/edit-profile.html.twig', [
            'editRecruiterProfileForm' => $form->createView(),
        ]);
    }
}
