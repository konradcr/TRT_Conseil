<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Form\CreateJobOfferType;
use App\Form\EditRecruiterProfileType;
use App\Repository\JobOfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecruiterController extends AbstractController
{
    #[Route('/recruiter', name: 'app_recruiter')]
    public function recruiterDashboard(JobOfferRepository $jobOfferRepository): Response
    {
        $recruiter = $this->getUser();
        $myJobOffers = $jobOfferRepository->findBy(['recruiter' => $recruiter]);

        return $this->render('recruiter/index.html.twig', [
            'myJobOffers' => $myJobOffers,
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

    #[Route('/recruiter/delete-job-offer/{id}', name: 'app_recruiter_delete_job_offer')]
    public function deleteJobOffer(int $id, JobOfferRepository $jobOfferRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$jobOfferRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'offre avec l\'id numéro %s n\'existe pas', $id));
        }

        $jobOffer = $jobOfferRepository->find($id);

        // recruiters can only delete their own job offers
        if ($jobOffer->getRecruiter() === $this->getUser()) {
            $entityManager->remove($jobOffer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recruiter');
    }
}
