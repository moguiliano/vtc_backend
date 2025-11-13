<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre email'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre numéro de téléphone'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre message',
                    'rows' => 5
                ]
            ]);
        // ⛔️ Pas de 'createdAt' ici, il est géré automatiquement dans le contrôleur
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
