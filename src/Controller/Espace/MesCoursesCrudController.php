<?php

namespace App\Controller\Espace;

use App\Entity\Reservation;
use App\Enum\EtatAbonne;
use App\Enum\ReservationStatus;
use App\Service\EntrepriseCourante;
use App\Service\Tarification;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * « Mes courses » de l'abonné (étape 2a, D-099, D-103) : même liste que « Réservations » de l'admin,
 * limitée aux courses de SON entreprise (tri automatique + condition explicite).
 * Il peut tout faire sur ses courses (consulter, modifier, statut, supprimer), sauf en lecture seule.
 * La création passe par « Nouvelle course » (carte, calcul du trajet, ses prix).
 */
#[IsGranted('ROLE_ABONNE')]
#[AdminCrud(routePath: '/mes-courses', routeName: 'mes_courses')]
class MesCoursesCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly Tarification $tarification,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Course')
            ->setEntityLabelInPlural('Mes courses')
            ->setPageTitle(Crud::PAGE_INDEX, 'Mes courses')
            ->setPageTitle(Crud::PAGE_DETAIL, fn (Reservation $r) => 'Course n°' . $r->getId())
            ->setPageTitle(Crud::PAGE_EDIT, fn (Reservation $r) => 'Modifier la course n°' . $r->getId())
            ->setDefaultSort(['dateHeureDepart' => 'DESC'])
            ->setSearchFields(['depart', 'arrivee', 'guestPrenom', 'guestTelephone'])
            ->hideNullValues();
    }

    public function configureActions(Actions $actions): Actions
    {
        $nouvelle = Action::new('nouvelleCourse', 'Nouvelle course', 'fa fa-plus')
            ->linkToRoute('espace_nouvelle_course')
            ->createAsGlobalAction()
            ->addCssClass('btn btn-primary');

        $actions = $actions
            ->disable(Action::NEW) // création : « Nouvelle course » (carte + prix)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);

        if ($this->modifiable()) {
            $actions->add(Crud::PAGE_INDEX, $nouvelle);
        } else {
            $actions->disable(Action::EDIT, Action::DELETE, Action::BATCH_DELETE);
        }

        return $actions;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $choix = [];
        foreach (ReservationStatus::cases() as $s) {
            $choix[$s->label()] = $s->value;
        }

        return $filters->add(ChoiceFilter::new('statut', 'Statut')->setChoices($choix));
    }

    public function configureFields(string $pageName): iterable
    {
        $choix = [];
        $badges = [];
        foreach (ReservationStatus::cases() as $s) {
            $choix[$s->label()] = $s->value;
            $badges[$s->value] = $s->badgeColor();
        }
        // Ses véhicules (ceux de sa grille), pas une liste figée
        $vehicules = [];
        foreach ($this->tarification->categoriesProposees() as $c) {
            $vehicules[$c->getLabel()] = $c->getSlug();
        }
        $r = $this->getContext()?->getEntity()?->getInstance();
        if ($r instanceof Reservation && $r->getTypeVehicule() && !in_array($r->getTypeVehicule(), $vehicules, true)) {
            $vehicules[$r->getTypeVehicule()] = $r->getTypeVehicule(); // véhicule décoché depuis : on le garde
        }

        yield ChoiceField::new('statut', 'Statut')->setChoices($choix)->renderAsBadges($badges)->setRequired(true);
        yield DateTimeField::new('createdAt', 'Créée le')->setFormat('dd/MM/yyyy HH:mm')->hideOnForm();
        yield TextField::new('depart', 'Lieu de départ');
        yield TextField::new('stopLieu', 'Arrêt')->hideOnIndex()->setRequired(false);
        yield TextField::new('arrivee', 'Lieu d\'arrivée');
        yield DateTimeField::new('dateHeureDepart', 'Date & heure')->setFormat('dd/MM/yyyy HH:mm');
        yield NumberField::new('distance', 'Distance (km)')->setNumDecimals(2)->hideOnIndex()->setRequired(false);
        yield NumberField::new('duree', 'Durée (min)')->hideOnIndex()->setRequired(false);
        yield ChoiceField::new('typeVehicule', 'Véhicule')->setChoices($vehicules);
        yield NumberField::new('prix', 'Prix (€)')->setNumDecimals(2);
        yield ChoiceField::new('modeReglement', 'Règlement')->setChoices(['Carte bancaire' => 'carte_bancaire', 'Espèces' => 'especes']);
        yield TextField::new('guestPrenom', 'Client');
        yield TextField::new('guestTelephone', 'Téléphone client');
        yield TextareaField::new('informationsComplementaires', 'Informations complémentaires')->hideOnIndex()->setRequired(false)->setNumOfRows(3);
    }

    /** Seulement SES courses (en plus du tri automatique par entreprise). */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.entreprise = :e')
            ->setParameter('e', $this->entrepriseCourante->get());
    }

    public function index(AdminContext $context)
    {
        if ($this->entrepriseCourante->get()->isEnAttente()) {
            return $this->redirectToRoute('espace_accueil');
        }

        return parent::index($context);
    }

    public function detail(AdminContext $context)
    {
        $this->sienneOu404($context);

        return parent::detail($context);
    }

    public function edit(AdminContext $context)
    {
        $this->sienneOu404($context);
        if (!$this->modifiable()) {
            throw $this->createAccessDeniedException();
        }

        return parent::edit($context);
    }

    public function delete(AdminContext $context)
    {
        $this->sienneOu404($context);
        if (!$this->modifiable()) {
            throw $this->createAccessDeniedException();
        }

        return parent::delete($context);
    }

    private function sienneOu404(AdminContext $context): Reservation
    {
        $r = $context->getEntity()->getInstance();
        if (!$r instanceof Reservation || $r->getEntreprise()?->getId() !== $this->entrepriseCourante->get()->getId()) {
            throw $this->createNotFoundException();
        }

        return $r;
    }

    private function modifiable(): bool
    {
        $e = $this->entrepriseCourante->get();

        return !$e->isEnAttente() && $e->getEtat() !== EtatAbonne::LECTURE_SEULE;
    }
}
