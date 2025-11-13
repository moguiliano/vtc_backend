<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
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
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des réservations');
    }

    public function configureFields(string $pageName): iterable
    {
        $depart = TextField::new('depart', 'Lieu de départ');
        $arrivee = TextField::new('arrivee', 'Lieu d\'arrivée');
        $dateHeureDepart = DateTimeField::new('dateHeureDepart', 'Date & Heure de départ');
        $stopOption = BooleanField::new('stopOption', 'Stop en route');
        $stopLieu = TextField::new('stopLieu', 'Lieu de stop')->onlyOnForms();
        $siegeBebe = BooleanField::new('siegeBebe', 'Siège bébé');
        $distance = NumberField::new('distance', 'Distance (km)')->setNumDecimals(2)->onlyOnDetail();
        $duree = NumberField::new('duree', 'Durée (min)')->onlyOnDetail();
        $typeVehicule = ChoiceField::new('typeVehicule', 'Type de véhicule')
            ->setChoices([
                'Green' => 'Green',
                'Berline' => 'Berline',
                'Van' => 'Van',
                'Grand Coffre' => 'Grand Coffre'
            ]);
        $prix = NumberField::new('prix', 'Prix (€)')->setNumDecimals(2)->onlyOnDetail();
        $user = AssociationField::new('user', 'Utilisateur')->onlyOnDetail();
        $isGuest = BooleanField::new('isGuest', 'Réservation en tant qu\'invité')->onlyOnDetail();
        $guestInfo = TextField::new('guestInfo', 'Informations invité')->onlyOnDetail();

        if (Crud::PAGE_INDEX === $pageName) {
            return [$depart, $arrivee, $dateHeureDepart, $typeVehicule, $prix];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [
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
                $user,
                $isGuest,
                $guestInfo
            ];
        } elseif (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            return [
                $depart,
                $arrivee,
                $dateHeureDepart,
                $stopOption,
                $stopLieu,
                $siegeBebe,
                $typeVehicule
            ];
        }
    }
}
