<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantController extends AbstractController
{
    #[Route('/consultant', name: 'app_consultant')]
    public function consultantDashboard(UserRepository $userRepository): Response
    {
        $usersToApprove = $userRepository->findBy(['isApproved' => 0]);

        return $this->render('consultant/index.html.twig', [
            'users' => $usersToApprove,
        ]);
    }

    #[Route('/consultant/approve-user/{id}', name: 'app_consultant_approve_user')]
    public function approveUser($id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le consultant avec l\'id numéro %s n\'existe pas', $id));
        }

        $consultant = $userRepository->find($id);
        $consultant->setIsApproved(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_consultant', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/consultant/disapprove-user/{id}', name: 'app_consultant_disapprove_user')]
    public function disapproveUser($id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le consultant avec l\'id numéro %s n\'existe pas', $id));
        }

        $consultant = $userRepository->find($id);
        $entityManager->remove($consultant);
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
