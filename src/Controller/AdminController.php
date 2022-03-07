<?php

namespace App\Controller;

use App\Entity\Consultant;
use App\Form\CreateConsultantType;
use App\Repository\ConsultantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[Route('/admin/create-consultant', name: 'app_admin_create_consultant')]
    public function createConsultant(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $consultant = new Consultant();
        $form = $this->createForm(CreateConsultantType::class, $consultant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $consultant->setPassword(
                $userPasswordHasher->hashPassword(
                    $consultant,
                    $form->get('plainPassword')->getData()
                )
            );
            $consultant->setRoles(["ROLE_CONSULTANT"]);
            $entityManager->persist($consultant);
            $entityManager->flush();


           return $this->redirectToRoute('app_admin');
        }
        return $this->render('admin/create-consultant.html.twig', [
            'createConsultantForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/delete-consultant/{id}', name: 'app_admin_delete_consultant')]
    public function removeConsultant(int $id, ConsultantRepository $consultantRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$consultantRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('Le consultant avec l\'id numéro %s n\'existe pas', $id));
        }

        $consultant = $consultantRepository->find($id);
        $entityManager->remove($consultant);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/profile', name: 'app_admin_profile')]
    public function adminProfile(): Response
    {
        return $this->render('admin/profile.html.twig', [
        ]);
    }
}
