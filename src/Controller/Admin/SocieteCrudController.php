<?php

namespace App\Controller\Admin;

use App\Entity\Societe;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class SocieteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Societe::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Société')
            ->setEntityLabelInPlural('Société')
            ->setPageTitle(Crud::PAGE_EDIT, 'Ma société — informations légales');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE)
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, fn(Action $a) => $a->setLabel('Enregistrer'))
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Societe) {
            $entityInstance->touch();
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addFieldset('Identité');
        yield TextField::new('raisonSociale', 'Raison sociale')->setColumns(6);
        yield TextField::new('nomCommercial', 'Nom commercial')->setColumns(6);
        yield TextField::new('formeJuridique', 'Forme juridique')->setHelp('Ex : SAS, SARL, EURL, EI')->setColumns(4);
        yield TextField::new('capitalSocial', 'Capital social')->setHelp('Ex : 1 000 € — facultatif')->setRequired(false)->setColumns(4);
        yield TextField::new('rcs', 'RCS')->setHelp('Ex : RCS Marseille 982 953 382 — facultatif')->setRequired(false)->setColumns(4);

        yield FormField::addFieldset('Numéros légaux');
        yield TextField::new('siren', 'SIREN')->setColumns(4);
        yield TextField::new('siret', 'SIRET (siège)')->setColumns(4);
        yield TextField::new('tvaIntra', 'N° TVA intracommunautaire')->setColumns(4);
        yield TextField::new('codeNaf', 'Code NAF / APE')->setColumns(4);
        yield TextField::new('numeroRevtc', 'N° REVTC')->setColumns(4);

        yield FormField::addFieldset('Coordonnées');
        yield TextField::new('adresse', 'Adresse')->setColumns(12);
        yield TextField::new('codePostal', 'Code postal')->setColumns(4);
        yield TextField::new('ville', 'Ville')->setColumns(8);
        yield TextField::new('telephone', 'Téléphone')->setColumns(6);
        yield EmailField::new('email', 'Email')->setRequired(false)->setColumns(6);
    }
}
