<?php

namespace App\Controller\Admin;

use App\Entity\MessageCategory;
use App\Enum\MessageTemplateType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * EasyAdmin > Messages > Catégories (D-029) : « Rappel », « Remerciement + promo »…
 */
#[IsGranted('ROLE_SUPER_ADMIN')]
class MessageCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MessageCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie de messages')
            ->setEntityLabelInPlural('Catégories de messages')
            ->setPageTitle(Crud::PAGE_INDEX, 'Catégories de messages')
            ->setHelp(Crud::PAGE_INDEX, 'Chaque catégorie apparaît dans un onglet de « Partager la course ». Ses modèles se créent dans Messages > Modèles de messages.')
            ->setDefaultSort(['type' => 'ASC', 'ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        $badges = [];
        foreach (MessageTemplateType::cases() as $case) {
            $badges[$case->value] = $case->badgeColor();
        }

        yield TextField::new('nom', 'Nom')
            ->setHelp('Ex. « Rappel », « Remerciement + promo », « Confirmation ».');
        yield ChoiceField::new('type', 'Onglet de Partager')
            ->setChoices(MessageTemplateType::choices())
            ->renderAsBadges($badges)
            ->setHelp('Chauffeur : toutes les infos. Groupe : zone approximative, sans le client. Client : envoyé au client.');
        yield IntegerField::new('ordre', 'Ordre')
            ->setHelp('La plus petite apparaît en premier.');
        yield IntegerField::new('nombreModeles', 'Modèles')->onlyOnIndex()->setSortable(false);
    }

    /** Une catégorie qui contient encore des modèles ne se supprime pas (sinon les modèles seraient perdus). */
    public function deleteEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof MessageCategory && !$entityInstance->getModeles()->isEmpty()) {
            $this->addFlash('danger', sprintf('La catégorie « %s » contient encore %d modèle(s) : déplacez-les ou supprimez-les d\'abord.',
                $entityInstance->getNom(), $entityInstance->getModeles()->count()));
            return;
        }
        parent::deleteEntity($em, $entityInstance);
    }
}
