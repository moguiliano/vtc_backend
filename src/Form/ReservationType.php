<?php

// src/Form/ReservationType.php
namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('depart', TextType::class, [
                'label' => 'Lieu de départ',
                'attr' => [
                    'autocomplete' => 'off',
                    'id' => 'reservation_depart'
                ]
            ])
            ->add('stopLieu', TextType::class, [
                'required' => false,
                'label' => 'Lieu d’arrêt (facultatif)',
                'attr' => [
                    'autocomplete' => 'off',
                    'id' => 'reservation_stopLieu'
                ]
            ])
            ->add('arrivee', TextType::class, [
                'label' => 'Lieu d’arrivée',
                'attr' => [
                    'autocomplete' => 'off',
                    'id' => 'reservation_arrivee'
                ]
            ])
            ->add('siegeBebe', CheckboxType::class, [
                'required' => false,
                'label' => 'Besoin d’un siège bébé ?'
            ])
            ->add('typeVehicule', TextType::class, [
                'label' => 'Type de véhicule'
            ])
           
            ->add('dateHeureDepart', DateTimeType::class, [
                'label' => 'Date et heure du départ',
                'required' => false,
                'widget' => 'single_text'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}