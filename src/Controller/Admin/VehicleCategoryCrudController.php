<?php

namespace App\Controller\Admin;

use App\Entity\VehicleCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

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
            ->setEntityLabelInPlural('Véhicules & Tarifs')
            ->setDefaultSort(['displayOrder' => 'ASC'])
            ->setPageTitle('index', 'Gestion des véhicules et tarifs')
            ->setHelp('index', 'Modifiez les prix directement ici. Les changements sont appliqués immédiatement sur le site.');
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

        yield NumberField::new('thresholdKm')
            ->setLabel('Seuil (km)')
            ->setHelp('Distance en km séparant les 2 formules de prix.')
            ->setNumDecimals(1)
            ->setColumns(3);

        yield NumberField::new('basePriceUnderThreshold')
            ->setLabel('Prix base ≤ seuil (€)')
            ->setNumDecimals(2)
            ->setColumns(3);

        yield NumberField::new('pricePerKmUnderThreshold')
            ->setLabel('€/km ≤ seuil')
            ->setNumDecimals(2)
            ->setColumns(3);

        yield NumberField::new('basePriceOverThreshold')
            ->setLabel('Prix base > seuil (€)')
            ->setNumDecimals(2)
            ->setColumns(3);

        yield NumberField::new('pricePerKmOverThreshold')
            ->setLabel('€/km > seuil')
            ->setNumDecimals(2)
            ->setColumns(3);

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
