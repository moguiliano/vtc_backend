<?php

// Commande pour générer ce formulaire :
// php bin/console make:form ReservationType

// src/Form/ReservationType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Reservation;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('depart', TextType::class, [
                'label' => 'Lieu de depart'
            ])
            ->add('arrivee', TextType::class, [
                'label' => 'Lieu d\'arrivee'
            ])
            ->add('dateHeureDepart', DateTimeType::class, [
                'label' => 'Date et heure de depart',
                'widget' => 'single_text'
            ])
            ->add('NumeroVol', TextType::class, [
                'label' => 'Numéro de vol/train',
                'required' => false
            ])
            ->add('Stop', CheckboxType::class, [
                'label' => 'Arrêt intermédiaire',
                'required' => false
            ])
            ->add('lieuArret', TextType::class, [
                'label' => 'Lieu d\'arrêt',
                'required' => false
            ])
            ->add('passagers', IntegerType::class, [
                'label' => 'Nombre de passagers'
            ])
            ->add('bagages', IntegerType::class, [
                'label' => 'Nombre de bagages'
            ])
            ->add('siegeBebe', CheckboxType::class, [
                'label' => 'Besoin d’un siège bebe',
                'required' => false
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom et prenom'
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Numero de telephone'
            ])
            ->add('Email', EmailType::class, [
                'label' => 'Adresse email'
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false
            ])
            ->add('TypeVehicule', ChoiceType::class, [
                'label' => 'Type de vehicule',
                'choices' => [
                    'Eco-Berline' => 'eco',
                    'Grand Coffre' => 'grand_coffre',
                    'Van' => 'van',
                    'Berline Premium' => 'premium',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Reserver'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
