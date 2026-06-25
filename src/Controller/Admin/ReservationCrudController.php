<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Enum\ReservationStatus;
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

class ReservationCrudController extends AbstractCrudController
{
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
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->isGranted('ROLE_RESERVATIONS_DELETE')) {
            $actions->disable(Action::DELETE, Action::BATCH_DELETE);
        }
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
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
        $createdAt = DateTimeField::new('createdAt', 'Créé le')->setFormat('dd/MM/yyyy HH:mm')->hideOnForm();

        // ── Détail seulement ──
        $user     = AssociationField::new('user', 'Utilisateur')->onlyOnDetail();
        $isGuest  = BooleanField::new('isGuest', 'Réservation invité')->onlyOnDetail();
        $guestInfo = TextField::new('guestInfo', 'guestInfo (legacy)')->onlyOnDetail();

        // ── Index ──
        if (Crud::PAGE_INDEX === $pageName) {
            return [$statut, $createdAt, $depart, $arrivee, $dateHeureDepart, $typeVehicule, $prix, $modeReglement, $guestPrenom, $guestTelephone, $infos];
        }

        // ── Détail ──
        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                $statut, $createdAt,
                $depart, $arrivee, $dateHeureDepart,
                $stopOption, $stopLieu, $siegeBebe,
                $distance, $duree,
                $typeVehicule, $prix, $modeReglement,
                $guestPrenom, $guestTelephone,
                $infos,
                $user, $isGuest, $guestInfo,
            ];
        }

        // ── Création / Édition ──
        return [
            $statut,
            $depart, $arrivee, $dateHeureDepart,
            $stopOption, $stopLieu, $siegeBebe,
            $distance, $duree,
            $typeVehicule, $prix, $modeReglement,
            $guestPrenom,
            $guestTelephone,
            $infos,
        ];
    }
}
