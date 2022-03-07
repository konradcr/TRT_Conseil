<?php

namespace App\Controller;

use App\Form\EditCandidateProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidateController extends AbstractController
{
    #[Route('/candidate', name: 'app_candidate')]
    public function candidateDashboard(): Response
    {
        return $this->render('candidate/index.html.twig', [
            'controller_name' => 'CandidateController',
        ]);
    }

    #[Route('/candidate/profile', name: 'app_candidate_profile')]
    public function candidateProfile(): Response
    {
        return $this->render('candidate/profile.html.twig', [
        ]);
    }

    #[Route('/candidate/profile/edit', name: 'app_candidate_edit_profile')]
    public function editRecruiterProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidate = $this->getUser();
        $form = $this->createForm(EditCandidateProfileType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidate_profile');
        }

        return $this->render('candidate/edit-profile.html.twig', [
            'editCandidateProfileForm' => $form->createView(),
        ]);
    }
}
