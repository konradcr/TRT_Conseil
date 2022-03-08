<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\Recruiter;
use App\Form\CreateConsultantType;
use App\Form\CreateJobOfferType;
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

    #[Route('/recruiter/post-job-offer', name: 'app_recruiter_post_job_offer')]
    public function postJobOffer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jobOffer = new JobOffer();
        $recruiter = $this->getUser();
        $form = $this->createForm(CreateJobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobOffer->setRecruiter($recruiter);
            $entityManager->persist($jobOffer);
            $entityManager->flush();

            return $this->redirectToRoute('app_recruiter');
        }
        return $this->render('recruiter/post-job-offer.html.twig', [
            'postJobOfferForm' => $form->createView(),
        ]);
    }
}
