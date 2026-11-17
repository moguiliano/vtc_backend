<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Enum\MessageTemplateType;
use App\Enum\ReservationStatus;
use App\Repository\MessageTemplateRepository;
use App\Service\PartageCourseService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ReservationCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator) {}

    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réservation')
            ->setEntityLabelInPlural('Réservations')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des réservations')
            ->setDefaultSort(['id' => 'DESC'])
            // Liste : une case vide reste vide (pas de badge « Null »)
            ->hideNullValues();
    }

    public function configureActions(Actions $actions): Actions
    {
        $pdfAction = Action::new('downloadPdf', 'Bon de commande PDF', 'fa fa-file-pdf')
            ->linkToRoute('reservation_pdf', fn(\App\Entity\Reservation $r) => ['id' => $r->getId()])
            ->setHtmlAttributes(['target' => '_blank'])
            ->addCssClass('btn btn-secondary');

        $factureAction = Action::new('createFacture', 'Créer une facture', 'fa fa-file-invoice')
            ->linkToUrl(fn(Reservation $r) => $this->adminUrlGenerator
                ->setController(FactureCrudController::class)
                ->setAction(Action::NEW)
                ->set('reservationId', $r->getId())
                ->generateUrl())
            ->displayIf(fn() => $this->isGranted('ROLE_RESERVATIONS_EDIT'))
            ->addCssClass('btn btn-secondary');

        // « Partager la course » (D-023) : toujours visible (D-027)
        $partagerAction = Action::new('partager', 'Partager la course', 'fa fa-share-alt')
            ->linkToCrudAction('partager')
            ->addCssClass('btn btn-primary');

        if (!$this->isGranted('ROLE_RESERVATIONS_DELETE')) {
            $actions->disable(Action::DELETE, Action::BATCH_DELETE);
        }
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $partagerAction)
            ->add(Crud::PAGE_INDEX, $partagerAction)
            ->add(Crud::PAGE_DETAIL, $pdfAction)
            ->add(Crud::PAGE_INDEX, $pdfAction)
            ->add(Crud::PAGE_DETAIL, $factureAction)
            ->add(Crud::PAGE_INDEX, $factureAction);
    }

    public function configureFields(string $pageName): iterable
    {
        // ── Statut badges ──
        $statutChoices = [];
        $statutBadges  = [];
        foreach (ReservationStatus::cases() as $case) {
            $statutChoices[$case->label()] = $case->value;
            $statutBadges[$case->value]    = $case->badgeColor();
        }

        $statut = ChoiceField::new('statut', 'Statut')
            ->setChoices($statutChoices)
            ->renderAsBadges($statutBadges)
            ->setRequired(true);

        // ── Trajet ──
        $depart          = TextField::new('depart', 'Lieu de départ');
        $arrivee         = TextField::new('arrivee', 'Lieu d\'arrivée');
        $dateHeureDepart = DateTimeField::new('dateHeureDepart', 'Date & Heure')->setFormat('dd/MM/yyyy HH:mm');
        $stopOption      = BooleanField::new('stopOption', 'Stop en route')->renderAsSwitch(false);
        $stopLieu        = TextField::new('stopLieu', 'Lieu de stop');
        $siegeBebe       = BooleanField::new('siegeBebe', 'Siège bébé')->renderAsSwitch(false);
        $distance        = NumberField::new('distance', 'Distance (km)')->setNumDecimals(2);
        $duree           = NumberField::new('duree', 'Durée (min)');
        $typeVehicule    = ChoiceField::new('typeVehicule', 'Véhicule')
            ->setChoices([
                'Eco Berline'  => 'eco_berline',
                'Berline'      => 'berline',
                'Grand Coffre' => 'grand_coffre',
                'Van'          => 'van',
            ]);
        $prix = NumberField::new('prix', 'Prix (€)')->setNumDecimals(2);
        $commission = NumberField::new('commission', 'Commission (€)')->setNumDecimals(2)
            ->setHelp('Fixée dans « Partager la course ». Vide = taux par défaut.');

        // ── Règlement ──
        $modeReglement = ChoiceField::new('modeReglement', 'Règlement')
            ->setChoices(['Carte bancaire' => 'carte_bancaire', 'Espèces' => 'especes'])
            ->renderAsBadges(['carte_bancaire' => 'info', 'especes' => 'success']);

        // ── Client ──
        $guestPrenom    = TextField::new('guestPrenom', 'Prénom client');
        $guestTelephone = TextField::new('guestTelephone', 'Téléphone client');
        $infos          = TextareaField::new('informationsComplementaires', 'Informations complémentaires')
            ->setNumOfRows(3)
            ->setRequired(false);

        // ── Dates ──
        $createdAt = DateTimeField::new('createdAt', 'Créé le')->setFormat('dd/MM/yyyy HH:mm');

        // ── Détail seulement ──
        $user     = AssociationField::new('user', 'Utilisateur')->onlyOnDetail();
        $isGuest  = BooleanField::new('isGuest', 'Réservation invité')->onlyOnDetail()
            ->setCssClass('')->setTemplatePath('admin/field/oui.html.twig');
        $guestInfo = TextField::new('guestInfo', 'guestInfo (legacy)')->onlyOnDetail();

        // ── Index ──
        if (Crud::PAGE_INDEX === $pageName) {
            return [$statut, $createdAt, $depart, $arrivee, $dateHeureDepart, $typeVehicule, $prix, $modeReglement, $guestPrenom, $guestTelephone, $infos];
        }

        // ── Détail : seuls les champs renseignés sont affichés ──
        // (pas de ligne « Siège bébé : Non », « Lieu de stop : Null »… ;
        //  « Stop en route » est sous-entendu par le lieu de stop)
        if (Crud::PAGE_DETAIL === $pageName) {
            // Sur la fiche, une case cochée s'affiche « Oui » comme les autres lignes
            // (par défaut EasyAdmin met un badge « YES » devant le libellé)
            $siegeBebe = BooleanField::new('siegeBebe', 'Siège bébé')
                ->setCssClass('')->setTemplatePath('admin/field/oui.html.twig');

            return $this->sansChampsVides([
                $statut, $createdAt,
                $depart, $stopLieu, $arrivee, $dateHeureDepart,
                $siegeBebe,
                $distance, $duree,
                $typeVehicule, $prix, $commission, $modeReglement,
                $guestPrenom, $guestTelephone,
                $infos,
                $user, $isGuest, $guestInfo,
            ]);
        }

        // ── Création / Édition ──
        return [
            $statut,
            $createdAt,
            $depart, $arrivee, $dateHeureDepart,
            $stopOption, $stopLieu, $siegeBebe,
            $distance, $duree,
            $typeVehicule, $prix, $modeReglement,
            $guestPrenom,
            $guestTelephone,
            $infos,
        ];
    }

    /**
     * Écran « Partager la course » (D-023 à D-028) : partage complet (chauffeur),
     * partiel (groupe) et messages client, avec SMS / WhatsApp / Copier.
     * POST : enregistre la commission choisie sur la réservation (D-024).
     *
     * #[AdminAction] : déclare la route « jolie » /admin/reservation/{id}/partager
     * (sans elle, EasyAdmin ne charge pas la réservation → erreur 500).
     */
    #[AdminAction(routePath: '/{entityId}/partager', routeName: 'partager', methods: ['GET', 'POST'])]
    public function partager(
        AdminContext $context,
        Request $request,
        PartageCourseService $partage,
        MessageTemplateRepository $templateRepo,
        EntityManagerInterface $em,
    ): Response {
        $reservation = $context->getEntity()->getInstance();
        if (!$reservation instanceof Reservation) {
            throw $this->createNotFoundException();
        }

        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction('partager')
            ->setEntityId($reservation->getId())
            ->generateUrl();

        if ($request->isMethod('POST')) {
            $this->enregistrerCommission($request, $reservation, $em);
            return $this->redirect($url);
        }

        $modeles = [];
        foreach (MessageTemplateType::cases() as $type) {
            $modeles[$type->value] = array_map(
                fn($t) => ['id' => $t->getId(), 'nom' => $t->getNom(), 'categorie' => $t->getCategorie()->getNom(), 'contenu' => $t->getContenu()],
                $templateRepo->findActifsParType($type)
            );
        }

        $prix       = (float) $reservation->getPrix();
        $commission = $partage->commission($reservation);
        $statut     = ReservationStatus::tryFrom($reservation->getStatut());

        return $this->render('admin/partage/index.html.twig', [
            'reservation'   => $reservation,
            'statut'        => $statut,
            'avertissements'=> $partage->avertissements($reservation),
            'peutModifier'  => $this->isGranted('ROLE_RESERVATIONS_EDIT'),
            'peutModeles'   => $this->isGranted('ROLE_SUPER_ADMIN'),
            'urlDetail'     => $this->adminUrlGenerator->setController(self::class)->setAction(Action::DETAIL)->setEntityId($reservation->getId())->generateUrl(),
            'urlModeles'    => $this->adminUrlGenerator->setController(MessageTemplateCrudController::class)->setAction(Action::INDEX)->unset('entityId')->generateUrl(),
            'commissionEnregistree' => $reservation->getCommission() !== null,
            'donnees'       => [
                'variables'       => $partage->variables($reservation),
                'prix'            => $prix,
                'commission'      => $commission,
                'pourcentDefaut'  => $partage->getCommissionPercentDefaut(),
                'telephoneClient' => $partage->telephoneClient($reservation),
                'modeles'         => $modeles,
            ],
        ]);
    }

    private function enregistrerCommission(Request $request, Reservation $reservation, EntityManagerInterface $em): void
    {
        if (!$this->isGranted('ROLE_RESERVATIONS_EDIT')) {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isCsrfTokenValid('partage_commission_' . $reservation->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Session expirée, recommencez.');
            return;
        }

        if ($request->request->has('reinitialiser')) {
            $reservation->setCommission(null);
            $em->flush();
            $this->addFlash('success', 'Commission remise au taux par défaut.');
            return;
        }

        $saisie = str_replace([',', ' ', '€'], ['.', '', ''], trim((string) $request->request->get('commission')));
        $prix   = (float) $reservation->getPrix();
        if (!is_numeric($saisie) || (float) $saisie < 0 || (float) $saisie > $prix) {
            $this->addFlash('danger', sprintf('Commission invalide : entre 0 et %s.', PartageCourseService::euros($prix)));
            return;
        }

        $reservation->setCommission(round((float) $saisie, 2));
        $em->flush();
        $this->addFlash('success', sprintf('Commission enregistrée : %s (net chauffeur %s).',
            PartageCourseService::euros($reservation->getCommission()),
            PartageCourseService::euros($prix - $reservation->getCommission())));
    }

    /**
     * Retire les champs sans valeur utile pour la réservation affichée :
     * null, texte vide, false (case non cochée) ou 0.
     *
     * @param FieldInterface[] $fields
     * @return FieldInterface[]
     */
    private function sansChampsVides(array $fields): array
    {
        $reservation = $this->getContext()?->getEntity()->getInstance();
        if (!$reservation instanceof Reservation) {
            return $fields;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return array_values(array_filter($fields, function (FieldInterface $field) use ($reservation, $accessor) {
            $value = $accessor->getValue($reservation, $field->getAsDto()->getProperty());

            return !(null === $value
                || false === $value
                || (is_string($value) && '' === trim($value))
                || (is_numeric($value) && 0.0 === (float) $value));
        }));
    }
}
