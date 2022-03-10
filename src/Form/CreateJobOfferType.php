<?php

namespace App\Form;

use App\Entity\JobOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateJobOfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Intitulé de l'offre",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Saisissez un intitulé.',
                    ]),
                ]
            ])
            ->add('location', TextType::class, [
                'label' => "Lieu de travail",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Saisissez un lieu de travail.',
                    ]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée (horaires, salaires, ...)',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Saisissez une description.',
                    ]),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier l\'annonce'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobOffer::class,
        ]);
    }
}
