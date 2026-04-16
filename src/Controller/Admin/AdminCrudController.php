<?php

namespace App\Controller\Admin;

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
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

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

        // Mot de passe — champ virtuel, non mappé
        yield TextField::new('plainPassword', 'Mot de passe')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type'            => PasswordType::class,
                'mapped'          => false,
                'required'        => $pageName === Crud::PAGE_NEW,
                'first_options'   => ['label' => 'Mot de passe', 'attr' => ['autocomplete' => 'new-password']],
                'second_options'  => ['label' => 'Confirmer le mot de passe'],
            ])
            ->onlyOnForms()
            ->setRequired($pageName === Crud::PAGE_NEW);

        yield ChoiceField::new('roles', 'Droits')
            ->setChoices([
                'Super Admin (accès total)'   => 'ROLE_SUPER_ADMIN',
                '— Voir réservations'         => 'ROLE_RESERVATIONS_VIEW',
                '— Modifier réservations'     => 'ROLE_RESERVATIONS_EDIT',
                '— Supprimer réservations'    => 'ROLE_RESERVATIONS_DELETE',
                '— Voir forfaits VTC'         => 'ROLE_FORFAITS_VIEW',
                '— Modifier forfaits VTC'     => 'ROLE_FORFAITS_EDIT',
                '— Voir véhicules'            => 'ROLE_VEHICULES_VIEW',
                '— Modifier véhicules'        => 'ROLE_VEHICULES_EDIT',
            ])
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_SUPER_ADMIN'         => 'danger',
                'ROLE_RESERVATIONS_VIEW'   => 'info',
                'ROLE_RESERVATIONS_EDIT'   => 'warning',
                'ROLE_RESERVATIONS_DELETE' => 'danger',
                'ROLE_FORFAITS_VIEW'       => 'info',
                'ROLE_FORFAITS_EDIT'       => 'warning',
                'ROLE_VEHICULES_VIEW'      => 'info',
                'ROLE_VEHICULES_EDIT'      => 'warning',
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
        // Récupère le mot de passe en clair depuis le formulaire
        $plain = $this->container->get('request_stack')
            ->getCurrentRequest()
            ?->request->all('Admin')['plainPassword']['first'] ?? null;

        if (!empty($plain)) {
            $admin->setPassword($this->hasher->hashPassword($admin, $plain));
        }
    }
}
