<?php

namespace App\Controller\Admin;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\SiteSettings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

/**
 * Écran « Avis Google » : une seule ligne, modifiable, ni création ni suppression.
 */
#[IsGranted('ROLE_SUPER_ADMIN')] // D-021 : réglages du site réservés au super-admin
class SiteSettingsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SiteSettings::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Avis Google')
            ->setEntityLabelInPlural('Avis Google')
            ->setPageTitle(Crud::PAGE_INDEX, 'Avis Google affichés sur le site')
            ->setHelp(Crud::PAGE_EDIT, 'Recopiez exactement la note et le nombre d\'avis affichés par votre fiche Google, le jour de la modification.');
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE);
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $actions->disable(Action::EDIT);
        }

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield NumberField::new('googleRating', 'Note Google (sur 5)')
            ->setNumDecimals(1)
            ->setStoredAsString()
            ->setHelp('Ex. 5,0 ou 4,9 : la note affichée sur votre fiche Google.');
        yield IntegerField::new('googleReviewCount', 'Nombre d\'avis Google');
        yield UrlField::new('googleReviewsUrl', 'Lien vers la fiche Google')
            ->setHelp('Lien ouvert par le bouton « Voir nos avis Google ».');
        yield DateTimeField::new('updatedAt', 'Dernière mise à jour')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->hideOnForm();
    }
}
