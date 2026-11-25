<?php

namespace App\Controller\Traits;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

/**
 * Colonnes de la grille de prix, communes à l'admin (« Grille de prix ») et à l'espace abonné
 * (« Mes prix ») : un seul endroit à modifier (D-093).
 */
trait GrilleTarifChampsTrait
{
    protected function champsGrille(string $pageName): iterable
    {
        yield AssociationField::new('categorie', 'Véhicule')
            ->setColumns(4)
            ->setFormTypeOption('disabled', $pageName === Crud::PAGE_EDIT);

        yield BooleanField::new('actif', 'Proposé')
            ->setHelp('Décoché : le véhicule n\'est pas proposé.')
            ->setColumns(2);

        yield NumberField::new('thresholdKm', 'Seuil (km)')
            ->setHelp('Distance en km séparant les 2 formules de prix.')
            ->setNumDecimals(1)
            ->setColumns(3);

        yield NumberField::new('basePriceUnderThreshold', 'Prix base ≤ seuil (€)')
            ->setNumDecimals(2)
            ->setColumns(3);

        yield NumberField::new('pricePerKmUnderThreshold', '€/km ≤ seuil')
            ->setNumDecimals(2)
            ->setColumns(3);

        // D-090 : calculé automatiquement (prise en charge + seuil × €/km), affiché pour information
        yield NumberField::new('basePriceOverThreshold', 'Prix au seuil (calculé)')
            ->setNumDecimals(2)
            ->hideOnForm()
            ->setHelp('Prise en charge + seuil × €/km jusqu\'au seuil : calculé automatiquement.');

        yield NumberField::new('pricePerKmOverThreshold', '€/km > seuil')
            ->setNumDecimals(2)
            ->setColumns(3);

        yield DateTimeField::new('updatedAt', 'Modifié le')->onlyOnDetail();
    }
}
