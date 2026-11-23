<?php

namespace App\Service;

use App\Entity\Entreprise;
use App\Entity\Forfait;
use App\Entity\Reservation;
use App\Enum\ReservationStatus;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Brique « Créer une réservation » (D-020).
 *
 * Tous les points d'entrée passent par ici : site public (accueil, /taxi-marseille, /reservation),
 * réservation par téléphone, quick-book public et admin. Demain : collaborateur et abonné.
 * Elle colle l'étiquette entreprise et range les infos client toujours de la même façon.
 * Les SMS et les redirections restent dans les contrôleurs (chaque parcours a les siens).
 */
class ReservationCreator
{
    public const MODES_REGLEMENT = ['especes', 'carte_bancaire'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EntrepriseCourante $entrepriseCourante,
    ) {}

    /** Enregistre une nouvelle réservation, rattachée à $entreprise (par défaut : l'entreprise courante). */
    public function enregistrer(Reservation $reservation, ?Entreprise $entreprise = null): Reservation
    {
        if ($entreprise !== null || $reservation->getEntreprise() === null) {
            $reservation->setEntreprise($entreprise ?? $this->entrepriseCourante->get());
        }

        $this->em->persist($reservation);
        $this->em->flush();

        return $reservation;
    }

    /**
     * Infos du client (non mappées par le formulaire) : prénom, téléphone, infos complémentaires, règlement.
     * Ne fait pas de flush : l'appelant enregistre (enregistrer() ou flush()).
     *
     * @param array<string, string|null> $infosEnPlus ajoutées au JSON legacy guestInfo (ex. email, source)
     */
    public function renseignerClient(
        Reservation $reservation,
        ?string $prenom,
        ?string $telephone,
        ?string $informations = null,
        mixed $modeReglement = null,
        array $infosEnPlus = [],
    ): Reservation {
        $prenom    = $prenom !== null && $prenom !== '' ? $prenom : null;
        $telephone = $telephone !== null && $telephone !== '' ? $telephone : null;

        $reservation->setGuestPrenom($prenom !== null ? mb_substr($prenom, 0, 100) : null);
        $reservation->setGuestTelephone($telephone !== null ? mb_substr($telephone, 0, 25) : null);
        $reservation->setGuestInfo(json_encode(array_filter(
            ['prenom' => $prenom, 'phone' => $telephone] + $infosEnPlus,
            fn ($v) => $v !== null && $v !== '',
        )));
        $reservation->setInformationsComplementaires($informations ? mb_substr($informations, 0, 500) : null);
        $reservation->setModeReglement(self::modeReglementValide($modeReglement));
        $reservation->setIsGuest(true);

        return $reservation;
    }

    /**
     * Réservation immédiate sur un forfait (quick-book public et admin) : éco-berline, confirmée,
     * départ entre maintenant et il y a 60 min ($offsetMinutes ≤ 0).
     */
    public function depuisForfait(Forfait $forfait, string $prenom, string $telephone, mixed $modeReglement, int $offsetMinutes): Reservation
    {
        $offset = max(-60, min(0, $offsetMinutes));

        $reservation = new Reservation();
        $reservation->setDepart($forfait->getDepart());
        $reservation->setArrivee($forfait->getArrivee());
        $reservation->setPrix((float) $forfait->getPrix());
        $reservation->setDistance($forfait->getDistance());
        $reservation->setDuree($forfait->getDuree());
        $reservation->setTypeVehicule('eco_berline');
        $reservation->setDateHeureDepart(new \DateTime("{$offset} minutes"));
        $reservation->setStopOption(false);
        $reservation->setSiegeBebe(false);
        $this->renseignerClient($reservation, $prenom, $telephone, null, $modeReglement);
        $reservation->setStatut(ReservationStatus::CONFIRMEE->value);
        $reservation->setCreatedAt(new \DateTimeImmutable('-10 minutes'));

        // La réservation appartient à l'entreprise qui propose le forfait
        return $this->enregistrer($reservation, $forfait->getEntreprise());
    }

    public static function modeReglementValide(mixed $mode): string
    {
        return in_array($mode, self::MODES_REGLEMENT, true) ? $mode : 'carte_bancaire';
    }

    /** Date (champ dateDepart) + heure (champ heureDepart) → un seul DateTime, ou null s'il en manque un. */
    public static function fusionnerDateHeure(?\DateTimeInterface $date, ?\DateTimeInterface $heure): ?\DateTime
    {
        if (!$date || !$heure) {
            return null;
        }

        return (new \DateTime())
            ->setDate((int) $date->format('Y'), (int) $date->format('m'), (int) $date->format('d'))
            ->setTime((int) $heure->format('H'), (int) $heure->format('i'));
    }
}
