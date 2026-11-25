<?php

namespace App\Form;

use App\Entity\Societe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/** « Ma société » de l'abonné (D-015) : saisie une fois, puis validée par le super-admin. */
class SocieteAbonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requis = fn (string $label, array $extra = []) => array_merge([
            'label' => $label,
            'empty_data' => '',
            'constraints' => [new Assert\NotBlank(message: 'Champ obligatoire.'), new Assert\Length(max: 150)],
        ], $extra);
        $facultatif = fn (string $label, array $extra = []) => array_merge([
            'label' => $label, 'required' => false, 'empty_data' => '',
            'constraints' => [new Assert\Length(max: 100)],
        ], $extra);

        $builder
            ->add('raisonSociale', TextType::class, $requis('Raison sociale', ['help' => 'Ex : DUPONT KARIM (EI) ou KD TRANSPORT SAS']))
            ->add('nomCommercial', TextType::class, $facultatif('Nom commercial'))
            ->add('formeJuridique', TextType::class, $requis('Forme juridique', ['help' => 'Ex : EI, micro-entreprise, SASU, SARL']))
            ->add('capitalSocial', TextType::class, $facultatif('Capital social', ['empty_data' => null]))
            ->add('rcs', TextType::class, $facultatif('RCS', ['empty_data' => null, 'help' => 'Ex : RCS Marseille 123 456 789']))
            ->add('siren', TextType::class, $requis('SIREN', ['constraints' => [new Assert\NotBlank(message: 'Champ obligatoire.'), new Assert\Regex('/^\d{3} ?\d{3} ?\d{3}$/', message: 'Le SIREN compte 9 chiffres.')]]))
            ->add('siret', TextType::class, $requis('SIRET', ['constraints' => [new Assert\NotBlank(message: 'Champ obligatoire.'), new Assert\Regex('/^\d{3} ?\d{3} ?\d{3} ?\d{5}$/', message: 'Le SIRET compte 14 chiffres.')]]))
            ->add('tvaIntra', TextType::class, $facultatif('N° TVA intracommunautaire', ['help' => 'Laisser vide si vous n\'êtes pas assujetti à la TVA']))
            ->add('codeNaf', TextType::class, $requis('Code NAF / APE', ['help' => 'Ex : 49.32Z']))
            ->add('numeroRevtc', TextType::class, $requis('N° d\'inscription au registre VTC (REVTC)', ['help' => 'Ex : EVTC013240001']))
            ->add('adresse', TextType::class, $requis('Adresse', ['constraints' => [new Assert\NotBlank(message: 'Champ obligatoire.'), new Assert\Length(max: 255)]]))
            ->add('codePostal', TextType::class, $requis('Code postal', ['constraints' => [new Assert\NotBlank(message: 'Champ obligatoire.'), new Assert\Length(max: 10)]]))
            ->add('ville', TextType::class, $requis('Ville', ['constraints' => [new Assert\NotBlank(message: 'Champ obligatoire.'), new Assert\Length(max: 100)]]))
            ->add('telephone', TextType::class, $requis('Téléphone', ['constraints' => [new Assert\NotBlank(message: 'Champ obligatoire.'), new Assert\Length(max: 30)]]))
            ->add('email', EmailType::class, $facultatif('E-mail affiché sur vos documents', ['empty_data' => null, 'constraints' => [new Assert\Email(), new Assert\Length(max: 150)]]));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Societe::class]);
    }
}
