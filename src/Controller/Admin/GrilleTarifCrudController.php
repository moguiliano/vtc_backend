<?php

namespace App\Controller\Admin;

use App\Entity\GrilleTarif;
use App\Service\EntrepriseCourante;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

/**
 * Grille de prix de l'entreprise courante (D-016, D-020) : une ligne par catégorie proposée.
 * Les prix étaient avant dans « Véhicules & Prix » ; les montants n'ont pas changé.
 */
class GrilleTarifCrudController extends AbstractCrudController
{
    public function __construct(private readonly EntrepriseCourante $entrepriseCourante) {}

    public static function getEntityFqcn(): string
    {
        return GrilleTarif::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Prix d\'un véhicule')
            ->setEntityLabelInPlural('Grille de prix')
            ->setPageTitle('index', 'Grille de prix')
            ->setHelp('index', 'Vos prix par catégorie de véhicule. Les changements sont appliqués immédiatement sur le site. '
                . 'Une catégorie n\'apparaît sur le site que si elle a une ligne ici, cochée « Proposé », et qu\'elle est active dans « Véhicules ».')
            ->setDefaultSort(['id' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE, Action::BATCH_DELETE);
    }

    /** Seulement la grille de l'entreprise courante. */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.entreprise = :entreprise')
            ->setParameter('entreprise', $this->entrepriseCourante->get());
    }

    public function createEntity(string $entityFqcn): GrilleTarif
    {
        return (new GrilleTarif())->setEntreprise($this->entrepriseCourante->get());
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof GrilleTarif) {
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        }
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('categorie', 'Véhicule')
            ->setColumns(4)
            ->setFormTypeOption('disabled', $pageName === Crud::PAGE_EDIT);

        yield BooleanField::new('actif', 'Proposé')
            ->setHelp('Décoché : le véhicule n\'est plus proposé sur le site.')
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

        yield NumberField::new('basePriceOverThreshold', 'Prix base > seuil (€)')
            ->setNumDecimals(2)
            ->setColumns(3);

        yield NumberField::new('pricePerKmOverThreshold', '€/km > seuil')
            ->setNumDecimals(2)
            ->setColumns(3);

        yield DateTimeField::new('updatedAt', 'Modifié le')->onlyOnDetail();
    }
}
