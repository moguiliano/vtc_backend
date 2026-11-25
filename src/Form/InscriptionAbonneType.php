<?php

namespace App\Form;

use App\Enum\TypeAbonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** Formulaire public « Créer mon espace » (D-074). Données non liées à une entité. */
class InscriptionAbonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de votre activité',
                'attr' => ['placeholder' => 'Ex : Karim VTC', 'maxlength' => 150],
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 150)],
            ])
            ->add('typeAbonnement', EnumType::class, [
                'class' => TypeAbonnement::class,
                'label' => 'Votre abonnement',
                'expanded' => true,
                'choice_label' => fn (TypeAbonnement $t) => sprintf('%s — %d € TTC / mois', $t->label(), $t->prixMensuelTtc()),
                'constraints' => [new Assert\NotNull(message: 'Choisissez un abonnement.')],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail (servira à vous connecter)',
                'constraints' => [new Assert\NotBlank(), new Assert\Email(), new Assert\Length(max: 180)],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => ['placeholder' => '06 12 34 56 78'],
                'constraints' => [new Assert\NotBlank(), new Assert\Regex('/^[0-9 +().-]{8,20}$/', message: 'Numéro de téléphone invalide.')],
            ])
            ->add('motDePasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
                'first_options' => ['label' => 'Mot de passe (8 caractères minimum)'],
                'second_options' => ['label' => 'Mot de passe (encore)'],
                'constraints' => [new Assert\NotBlank(), new Assert\Length(min: 8, max: 4096)],
            ])
            // Piège à robots (D-075) : champ invisible pour un humain, à laisser vide.
            ->add('siteWeb', TextType::class, [
                'required' => false,
                'label' => 'Ne pas remplir ce champ',
                'attr' => ['autocomplete' => 'off', 'tabindex' => '-1'],
            ]);
    }
}
