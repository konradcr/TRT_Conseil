<?php

namespace App\Controller;

use App\Repository\JobOfferRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantController extends AbstractController
{
    #[Route('/consultant', name: 'app_consultant')]
    public function consultantDashboard(UserRepository $userRepository, JobOfferRepository $jobOfferRepository): Response
    {
        $usersToApprove = $userRepository->findBy(['isApproved' => 0]);
        $jobOffersToApprove = $jobOfferRepository->findBy(['isApproved' => 0]);

        return $this->render('consultant/index.html.twig', [
            'users' => $usersToApprove,
            'jobOffers' => $jobOffersToApprove
        ]);
    }

    #[Route('/consultant/approve-user/{id}', name: 'app_consultant_approve_user')]
    public function approveUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'utilisateur avec l\'id numéro %s n\'existe pas', $id));
        }

        $user = $userRepository->find($id);
        $user->setIsApproved(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_consultant');
    }

    #[Route('/consultant/disapprove-user/{id}', name: 'app_consultant_disapprove_user')]
    public function disapproveUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'utilisateur avec l\'id numéro %s n\'existe pas', $id));
        }

        $user = $userRepository->find($id);
        $userRoles = $user->getRoles();

        // consultant can only disapprove recruiters and candidates
        if (in_array('ROLE_RECRUITER', $userRoles) || in_array('ROLE_CANDIDATE', $userRoles)) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_consultant');
    }

    #[Route('/consultant/approve-job-offer/{id}', name: 'app_consultant_approve_job_offer')]
    public function approveJobOffer(int $id, JobOfferRepository $jobOfferRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$jobOfferRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'offre avec l\'id numéro %s n\'existe pas', $id));
        }

        $jobOffer = $jobOfferRepository->find($id);
        $jobOffer->setIsApproved(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_consultant');
    }

    #[Route('/consultant/disapprove-job-offer/{id}', name: 'app_consultant_disapprove_job_offer')]
    public function disapproveJobOffer(int $id, JobOfferRepository $jobOfferRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$jobOfferRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'offre avec l\'id numéro %s n\'existe pas', $id));
        }

        $jobOffer = $jobOfferRepository->find($id);

        $entityManager->remove($jobOffer);
        $entityManager->flush();

        return $this->redirectToRoute('app_consultant');
    }

    #[Route('/consultant/profile', name: 'app_consultant_profile')]
    public function consultantProfile(): Response
    {
        return $this->render('consultant/profile.html.twig', [
        ]);
    }
}
