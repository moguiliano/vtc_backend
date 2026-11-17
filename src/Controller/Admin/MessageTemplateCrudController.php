<?php

namespace App\Controller\Admin;

use App\Entity\MessageTemplate;
use App\Service\PartageCourseService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * EasyAdmin > Messages > Modèles de messages (D-028, D-029) : textes utilisés par « Partager la course »,
 * chacun rangé dans une catégorie.
 */
#[IsGranted('ROLE_SUPER_ADMIN')]
class MessageTemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MessageTemplate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $aide = 'Variables remplacées automatiquement : ';
        foreach (PartageCourseService::VARIABLES as $variable => $sens) {
            $aide .= sprintf('<code>%s</code> %s · ', $variable, htmlspecialchars($sens));
        }
        $aide .= '<br>Une ligne dont une variable est vide (ex. pas d\'arrêt) disparaît du message.';

        return $crud
            ->setEntityLabelInSingular('Modèle de message')
            ->setEntityLabelInPlural('Modèles de messages')
            ->setPageTitle(Crud::PAGE_INDEX, 'Modèles de messages (Partager la course)')
            ->setHelp(Crud::PAGE_INDEX, 'Textes proposés dans « Partager la course », sur une réservation.')
            ->setHelp(Crud::PAGE_EDIT, $aide)
            ->setHelp(Crud::PAGE_NEW, $aide)
            ->setDefaultSort(['categorie' => 'ASC', 'ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield AssociationField::new('categorie', 'Catégorie')
            ->setRequired(true)
            ->setHelp('Pas la bonne ? Créez-la dans Messages > Catégories.');
        yield TextareaField::new('contenu', 'Texte du message')
            ->setNumOfRows(14)
            ->hideOnIndex();
        yield BooleanField::new('actif', 'Actif');
        yield IntegerField::new('ordre', 'Ordre')
            ->setHelp('Le plus petit apparaît en premier.');
    }
}
