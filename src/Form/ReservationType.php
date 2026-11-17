<?php

// src/Form/ReservationType.php
namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                'label' => 'Type de véhicule',
                'required' => true,
                'attr' => ['id' => 'reservation_typeVehicule']
            ])


            ->add('dateDepart', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'data' => new \DateTime('now', new \DateTimeZone('Europe/Paris')), // aujourd’hui, heure de Marseille (comme l’heure)
                'mapped' => false, // <- important
                'attr' => ['class' => 'form-control'],
                'required' => true,

            ])
            ->add('heureDepart', TimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'data' => \DateTimeImmutable::createFromFormat(
                    'H:i',
                    (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('H:i')
                ),
                'mapped' => false, // 🔥 évite l'erreur, pas lié à l'entité
                'label' => 'Heure de départ',
                'with_seconds' => false,
                'attr' => ['class' => 'form-control'],
                'required' => true,

            ])



            ->add('duree', NumberType::class, [
                'label' => 'Durée (minutes)',
                'scale' => 2,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Durée estimée',
                    'class' => 'form-control',
                ],
            ])
            ->add('distance', NumberType::class, [
                'label' => 'Distance (km)',
                'scale' => 2,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Distance estimée',
                    'class' => 'form-control',
                ],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix estimé (€)',
                'scale' => 2,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prix total',
                    'class' => 'form-control',
                    'attr'=>'hidden',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
