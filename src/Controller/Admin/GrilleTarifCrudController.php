<?php

namespace App\Controller\Admin;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Controller\Traits\GrilleTarifChampsTrait;
use App\Entity\GrilleTarif;
use App\Service\EntrepriseCourante;
use App\Service\GrilleAbonnes;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

/**
 * Grille de prix de l'entreprise courante (D-016, D-020) : une ligne par catégorie proposée.
 * Les prix étaient avant dans « Véhicules & Prix » ; les montants n'ont pas changé.
 */
#[IsGranted('ROLE_SUPER_ADMIN')] // D-021 : réservé au super-admin, même en tapant l'adresse
class GrilleTarifCrudController extends AbstractCrudController
{
    use GrilleTarifChampsTrait;

    public function __construct(
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly GrilleAbonnes $grilleAbonnes,
    ) {}

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
        $this->propager($entityInstance);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->propager($entityInstance);
    }

    /** D-091 : les abonnés « comme ZenCAR » suivent le nouveau prix. */
    private function propager($grille): void
    {
        if ($grille instanceof GrilleTarif && ($n = $this->grilleAbonnes->propagerDepuis($grille)) > 0) {
            $this->addFlash('info', sprintf('Prix repris par %d ligne(s) d\'abonnés « comme ZenCAR ».', $n));
        }
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $this->assetsGrille($assets); // prix au seuil et exemple en direct (D-090)
    }

    public function configureFields(string $pageName): iterable
    {
        return $this->champsGrille($pageName); // mêmes colonnes que « Mes prix » des abonnés (D-093)
    }
}
