<?php

namespace App\Controller;

use App\Entity\JobApplication;
use App\Form\EditCandidateProfileType;
use App\Repository\JobOfferRepository;
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
    public function candidateDashboard(JobOfferRepository $jobOfferRepository): Response
    {
        $candidate = $this->getUser();
        $jobOffers = $jobOfferRepository->findBy(['isApproved' => 1]);

        // check if the candidate already applied for the job
        foreach ($jobOffers as $jobOffer) {
            $applications = $jobOffer->getJobApplications();
            $jobOffer->hasApplied = false ;
            foreach ($applications as $application) {
                if ($application->getCandidate() === $candidate) {
                    $jobOffer->hasApplied = true ;
                }
            }
        }

        return $this->render('candidate/index.html.twig', [
            'jobOffers' => $jobOffers,
        ]);
    }

    #[Route('/candidate/apply-job-offer/{id}', name: 'app_candidate_apply_job_offer')]
    public function applyToJob(int $id, Request $request, EntityManagerInterface $entityManager, JobOfferRepository $jobOfferRepository): Response
    {
        if (!$jobOfferRepository->find($id)) {
            throw $this->createNotFoundException(sprintf('L\'offre avec l\'id numéro %s n\'existe pas', $id));
        }

        $jobOffer = $jobOfferRepository->find($id);
        $candidate = $this->getUser();

        $jobApplication = new JobApplication();

        $jobApplication->setCandidate($candidate);
        $jobApplication->setJobOffer($jobOffer);

        $jobOffer->addJobApplication($jobApplication);
        $entityManager->persist($jobApplication);
        $entityManager->persist($jobOffer);
        $entityManager->flush();

        return $this->redirectToRoute('app_candidate');
    }

    #[Route('/candidate/profile', name: 'app_candidate_profile')]
    public function candidateProfile(): Response
    {
        return $this->render('candidate/profile.html.twig', [
        ]);
    }

    #[Route('/candidate/profile/edit', name: 'app_candidate_edit_profile')]
    public function editCandidateProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
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
