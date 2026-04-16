<?php

namespace App\Controller\Admin;

use App\Entity\Forfait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class ForfaitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Forfait::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Forfait VTC')
            ->setEntityLabelInPlural('Forfaits VTC')
            ->setDefaultSort(['ordre' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->isGranted('ROLE_FORFAITS_EDIT')) {
            $actions->disable(Action::EDIT, Action::NEW, Action::DELETE, Action::BATCH_DELETE);
        }
        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('depart', 'Départ');
        yield TextField::new('arrivee', 'Arrivée');
        yield IntegerField::new('prix', 'Prix (€)');
        yield TextField::new('icone', 'Icône Bootstrap')
            ->setHelp('Ex: bi bi-airplane-fill, bi bi-train-front-fill')
            ->setRequired(false);
        yield IntegerField::new('ordre', 'Ordre d\'affichage');
        yield BooleanField::new('actif', 'Actif');
    }
}
