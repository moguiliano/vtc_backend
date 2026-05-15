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
        if (!$this->isGranted('ROLE_RESERVATIONS_EDIT')) {
            $actions->disable(Action::NEW);
        }
        if (!$this->isGranted('ROLE_RESERVATIONS_DELETE')) {
            $actions->disable(Action::DELETE, Action::BATCH_DELETE);
        }
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        // ── Status choices array (label → value string for EasyAdmin) ──
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

        $depart          = TextField::new('depart', 'Lieu de départ');
        $arrivee         = TextField::new('arrivee', 'Lieu d\'arrivée');
        $dateHeureDepart = DateTimeField::new('dateHeureDepart', 'Date & Heure')->setFormat('dd/MM/yyyy HH:mm');
        $stopOption      = BooleanField::new('stopOption', 'Stop');
        $stopLieu        = TextField::new('stopLieu', 'Lieu de stop')->onlyOnForms();
        $siegeBebe       = BooleanField::new('siegeBebe', 'Siège bébé');
        $distance        = NumberField::new('distance', 'Distance (km)')->setNumDecimals(2)->onlyOnDetail();
        $duree           = NumberField::new('duree', 'Durée (min)')->onlyOnDetail();
        $typeVehicule    = ChoiceField::new('typeVehicule', 'Véhicule')
            ->setChoices([
                'Eco Berline'  => 'eco_berline',
                'Berline'      => 'berline',
                'Grand Coffre' => 'grand_coffre',
                'Van'          => 'van',
            ]);
        $prix            = NumberField::new('prix', 'Prix (€)')->setNumDecimals(2);
        $modeReglement   = ChoiceField::new('modeReglement', 'Règlement')
            ->setChoices(['Carte bancaire' => 'carte_bancaire', 'Espèces' => 'especes'])
            ->renderAsBadges(['carte_bancaire' => 'info', 'especes' => 'success']);
        $infos           = TextareaField::new('informationsComplementaires', 'Notes client')
            ->setNumOfRows(3)->onlyOnDetail();
        $user            = AssociationField::new('user', 'Utilisateur')->onlyOnDetail();
        $isGuest         = BooleanField::new('isGuest', 'Invité')->onlyOnDetail();
        $guestInfo       = TextField::new('guestInfo', 'Info invité')->onlyOnDetail();

        if (Crud::PAGE_INDEX === $pageName) {
            return [$statut, $depart, $arrivee, $dateHeureDepart, $typeVehicule, $prix, $modeReglement];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                $statut,
                $depart,
                $arrivee,
                $dateHeureDepart,
                $stopOption,
                $stopLieu,
                $siegeBebe,
                $distance,
                $duree,
                $typeVehicule,
                $prix,
                $modeReglement,
                $infos,
                $user,
                $isGuest,
                $guestInfo,
            ];
        }

        // PAGE_NEW / PAGE_EDIT
        return [
            $statut,
            $depart,
            $arrivee,
            $dateHeureDepart,
            $stopOption,
            $stopLieu,
            $siegeBebe,
            $typeVehicule,
            $prix,
            $modeReglement,
        ];
    }
}
