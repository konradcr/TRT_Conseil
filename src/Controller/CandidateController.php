<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\EditCandidateProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function editRecruiterProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $candidate = $this->getUser();
        $form = $this->createForm(EditCandidateProfileType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $cvFile */
            $cvFile = $form->get('cvPath')->getData();

            // this condition is needed because the 'cvPath' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cvFile->move(
                        $this->getParameter('cv_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', $e->getMessage());
                }
                $candidate->setCvPath($newFilename);
            }
            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidate_profile');
        }

        return $this->render('candidate/edit-profile.html.twig', [
            'editCandidateProfileForm' => $form->createView(),
        ]);
    }
}
