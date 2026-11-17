<?php

namespace App\Controller\Admin;

use App\Entity\Appel;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Journal des appels : saisie rapide après chaque appel (mobile).
 */
#[IsGranted('ROLE_ADMIN')]
class AppelCrudController extends AbstractCrudController
{
    private ?QueryBuilder $filteredQuery = null;

    public static function getEntityFqcn(): string
    {
        return Appel::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Appel')
            ->setEntityLabelInPlural('Journal des appels')
            ->setPageTitle(Crud::PAGE_NEW, 'Noter un appel')
            ->setDefaultSort(['dateAppel' => 'DESC'])
            ->setPaginatorPageSize(50)
            ->overrideTemplate('crud/index', 'admin/appel/index.html.twig');
    }

    /** Garde une copie de la requête filtrée pour calculer les totaux du résumé. */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $this->filteredQuery = clone $qb;

        return $qb;
    }

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName') && null !== $this->filteredQuery) {
            $responseParameters->set('resume', $this->calculerResume($this->filteredQuery));
        }

        return $responseParameters;
    }

    /**
     * Totaux par source sur les appels filtrés : nombre d'appels, courses gagnées
     * (faites + dispatchées), refus et gain total.
     *
     * @return array{lignes: list<array{source: string, appels: int, courses: int, refus: int, gain: float}>, total: array{appels: int, courses: int, refus: int, gain: float}}
     */
    private function calculerResume(QueryBuilder $qb): array
    {
        $alias = $qb->getRootAliases()[0];
        $rows = (clone $qb)
            ->resetDQLPart('orderBy')
            ->select(sprintf(
                '%1$s.source AS source, COUNT(%1$s.id) AS appels, '
                . "SUM(CASE WHEN %1\$s.statut IN ('faite', 'dispatchee') THEN 1 ELSE 0 END) AS courses, "
                . "SUM(CASE WHEN %1\$s.statut = 'refusee' THEN 1 ELSE 0 END) AS refus, "
                . 'COALESCE(SUM(%1$s.gain), 0) AS gain',
                $alias
            ))
            ->groupBy(sprintf('%s.source', $alias))
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getArrayResult();

        $libelles = array_flip(Appel::SOURCES);
        $total = ['appels' => 0, 'courses' => 0, 'refus' => 0, 'gain' => 0.0];
        $lignes = [];
        foreach ($rows as $row) {
            $ligne = [
                'source'  => $libelles[$row['source']] ?? $row['source'],
                'appels'  => (int) $row['appels'],
                'courses' => (int) $row['courses'],
                'refus'   => (int) $row['refus'],
                'gain'    => (float) $row['gain'],
            ];
            foreach (['appels', 'courses', 'refus', 'gain'] as $k) {
                $total[$k] += $ligne[$k];
            }
            $lignes[] = $ligne;
        }
        usort($lignes, fn (array $a, array $b) => $b['gain'] <=> $a['gain']);

        return ['lignes' => $lignes, 'total' => $total];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $a) => $a->setLabel('Noter un appel')->setIcon('fa fa-phone'));
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('dateAppel', 'Date de l\'appel'))
            ->add(ChoiceFilter::new('source', 'Source')->setChoices(Appel::SOURCES))
            ->add(ChoiceFilter::new('statut', 'Statut')->setChoices(Appel::STATUTS))
            ->add(ChoiceFilter::new('motifRefus', 'Motif de refus')->setChoices(Appel::MOTIFS_REFUS));
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('dateAppel', 'Appel le')
            ->setFormat('EEE dd/MM HH:mm')
            ->setHelp('Pré-rempli avec l\'heure actuelle : corrige si tu notes plus tard.');

        yield ChoiceField::new('source', 'Source')
            ->setChoices(Appel::SOURCES)
            ->renderExpanded()
            ->setFormTypeOption('label_attr', ['class' => 'radio-inline'])
            ->setColumns(12);

        yield ChoiceField::new('statut', 'Statut')
            ->setChoices(Appel::STATUTS)
            ->renderExpanded()
            ->setFormTypeOption('label_attr', ['class' => 'radio-inline'])
            ->renderAsBadges([
                'faite' => 'success', 'dispatchee' => 'info', 'refusee' => 'danger',
                'sans_suite' => 'secondary', 'annulee' => 'warning',
            ]);

        yield ChoiceField::new('motifRefus', 'Motif du refus')
            ->setChoices(Appel::MOTIFS_REFUS)
            ->renderExpanded()
            ->setFormTypeOption('placeholder', 'Aucun')
            ->setFormTypeOption('label_attr', ['class' => 'radio-inline'])
            ->setRequired(false)
            ->setHelp('Seulement si refusée.');

        yield ChoiceField::new('typeService', 'Type')
            ->setChoices(Appel::TYPES)
            ->renderExpanded()
            ->setFormTypeOption('placeholder', 'Non précisé')
            ->setFormTypeOption('label_attr', ['class' => 'radio-inline'])
            ->setRequired(false);

        yield DateTimeField::new('dateCourse', 'Course le')
            ->setFormat('EEE dd/MM HH:mm')
            ->setRequired(false)
            ->setHelp('Si la course est à une autre heure que l\'appel (réservation à l\'avance).');

        yield TextField::new('trajet', 'Trajet')
            ->setRequired(false)
            ->setHelp('Ex. Aéroport → Vieux-Port');

        yield MoneyField::new('prixCourse', 'Prix course')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setRequired(false)
            ->setHelp('Ce que le client a payé.');

        yield MoneyField::new('gain', 'Mon gain')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setRequired(false)
            ->setHelp('Course faite : le prix. Dispatchée : ta commission. Refusée : vide.');

        yield TextareaField::new('observation', 'Observation')
            ->setRequired(false)
            ->hideOnIndex();

        yield DateTimeField::new('createdAt', 'Saisi le')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->hideOnForm()
            ->hideOnIndex();
    }
}
