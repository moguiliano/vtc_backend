<?php

namespace App\Controller\Admin;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\VehicleCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

#[IsGranted('ROLE_SUPER_ADMIN')] // D-021 : réservé au super-admin, même en tapant l'adresse
class VehicleCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VehicleCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Véhicule')
            ->setEntityLabelInPlural('Véhicules')
            ->setDefaultSort(['displayOrder' => 'ASC'])
            ->setPageTitle('index', 'Catégories de véhicules')
            ->setHelp('index', 'Catégories communes (nom, places, bagages, ordre). Les prix se modifient dans « Grille de prix ».');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('slug')
            ->setLabel('Slug technique')
            ->setHelp('Identifiant interne : eco_berline, grand_coffre, berline, van. Ne pas modifier.')
            ->setColumns(4);

        yield TextField::new('label')
            ->setLabel('Nom affiché')
            ->setColumns(4);

        yield BooleanField::new('isActive')
            ->setLabel('Actif')
            ->setColumns(2);

        yield IntegerField::new('displayOrder')
            ->setLabel('Ordre d\'affichage')
            ->setColumns(2);

        yield TextareaField::new('description')
            ->setLabel('Description')
            ->hideOnIndex()
            ->setColumns(12);

        yield IntegerField::new('maxPassengers')
            ->setLabel('Passagers max')
            ->setColumns(3);

        yield IntegerField::new('luggageCapacity')
            ->setLabel('Capacité bagages')
            ->setColumns(3);

        yield DateTimeField::new('createdAt')
            ->setLabel('Créé le')
            ->onlyOnDetail();

        yield DateTimeField::new('updatedAt')
            ->setLabel('Modifié le')
            ->onlyOnDetail();
    }
}
