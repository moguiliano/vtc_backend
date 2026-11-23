<?php

namespace App\Controller\Admin;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[IsGranted('ROLE_SUPER_ADMIN')] // D-021 : réservé au super-admin, même en tapant l'adresse
class AdminCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private Security $security,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Admin::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Administrateur')
            ->setEntityLabelInPlural('Administrateurs')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des administrateurs')
            ->setHelp(Crud::PAGE_NEW, 'Le mot de passe sera hashé automatiquement.')
            ->setHelp(Crud::PAGE_EDIT, 'Laissez le mot de passe vide pour ne pas le modifier.');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom / Prénom');
        yield EmailField::new('email', 'Email');
        yield \EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField::new('createdAt', 'Créé le')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->hideOnForm();

        // Mot de passe — champ virtuel, non mappé
        yield TextField::new('plainPassword', 'Mot de passe')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type'            => PasswordType::class,
                'mapped'          => true,
                'required'        => $pageName === Crud::PAGE_NEW,
                'first_options'   => ['label' => 'Mot de passe', 'attr' => ['autocomplete' => 'new-password']],
                'second_options'  => ['label' => 'Confirmer le mot de passe'],
            ])
            ->onlyOnForms()
            ->setRequired($pageName === Crud::PAGE_NEW);

        // D-064 : 2 droits au choix dans le formulaire. Les anciens droits
        // détaillés restent lisibles dans la liste pour les comptes qui les ont encore.
        $libelles = [
            'ROLE_SUPER_ADMIN'         => 'Super Admin (accès total)',
            'ROLE_REGULATEUR'          => 'Régulateur (courses au quotidien)',
            'ROLE_ADMIN'               => 'Admin (ancien droit)',
            'ROLE_RESERVATIONS_VIEW'   => 'Voir réservations (ancien droit)',
            'ROLE_RESERVATIONS_EDIT'   => 'Modifier réservations (ancien droit)',
            'ROLE_RESERVATIONS_DELETE' => 'Supprimer réservations (ancien droit)',
            'ROLE_FORFAITS_VIEW'       => 'Voir forfaits VTC (ancien droit)',
            'ROLE_FORFAITS_EDIT'       => 'Modifier forfaits VTC (ancien droit)',
            'ROLE_VEHICULES_VIEW'      => 'Voir véhicules (ancien droit)',
            'ROLE_VEHICULES_EDIT'      => 'Modifier véhicules (ancien droit)',
        ];
        $formulaire = in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true);
        $choix = $formulaire ? array_slice($libelles, 0, 2, true) : $libelles;

        yield ChoiceField::new('roles', 'Droits')
            ->setChoices(array_flip($choix))
            ->allowMultipleChoices()
            ->renderExpanded($formulaire)
            ->setHelp('Régulateur : gère les courses (statuts, réservations téléphone, partage, bons de commande, factures en lecture). '
                . 'Il ne voit ni les tarifs, ni « Ma société », ni les comptes admin, et ne crée pas de factures.')
            ->renderAsBadges([
                'ROLE_SUPER_ADMIN' => 'danger',
                'ROLE_REGULATEUR'  => 'primary',
                'ROLE_ADMIN'       => 'warning',
            ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    /**
     * Hash le mot de passe avant persist/flush
     */
    public function persistEntity(EntityManagerInterface $em, mixed $entity): void
    {
        $this->hashPasswordIfProvided($entity);
        parent::persistEntity($em, $entity);
    }

    public function updateEntity(EntityManagerInterface $em, mixed $entity): void
    {
        $this->hashPasswordIfProvided($entity);
        parent::updateEntity($em, $entity);
    }

    private function hashPasswordIfProvided(Admin $admin): void
    {
        $plain = $admin->getPlainPassword();
        if (!empty($plain)) {
            $admin->setPassword($this->hasher->hashPassword($admin, $plain));
            $admin->eraseCredentials();
        }
    }
}
