<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Societe;
use App\Enum\ReservationStatus;
use App\Repository\SiteSettingsRepository;
use App\Repository\VehicleCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * « Partager la course » (D-023 à D-028) : prépare les valeurs qui remplacent
 * les variables des modèles de messages ({prenom}, {date_course}…).
 *
 * Le remplacement lui-même se fait dans le navigateur (public/js/partage-course.js),
 * pour que la commission et la zone modifiées se voient tout de suite dans l'aperçu.
 */
class PartageCourseService
{
    /** Variables proposées dans l'aide de EasyAdmin > Modèles de messages. */
    public const VARIABLES = [
        '{prenom}'           => 'prénom du client',
        '{telephone_client}' => 'téléphone du client',
        '{date_course}'      => 'jour et heure, ex. « mardi 18 novembre à 14h30 »',
        '{depart}'           => 'adresse de départ exacte',
        '{zone}'             => 'zone de départ approximative (modifiable au partage)',
        '{arrivee}'          => 'adresse d\'arrivée',
        '{zone_arrivee}'     => 'zone d\'arrivée approximative',
        '{arret}'            => 'arrêt intermédiaire',
        '{vehicule}'         => 'type de véhicule',
        '{options}'          => 'options (siège bébé)',
        '{distance}'         => 'distance en km',
        '{duree}'            => 'durée estimée',
        '{prix}'             => 'prix à encaisser',
        '{mode_reglement}'   => 'carte bancaire ou espèces',
        '{commission}'       => 'commission ZenCAR',
        '{net_chauffeur}'    => 'prix moins la commission',
        '{infos}'            => 'informations complémentaires du client',
        '{reference}'        => 'numéro de la réservation',
        '{lien_avis_google}' => 'lien de la fiche Google (EasyAdmin > Avis Google)',
        '{telephone}'        => 'téléphone ZenCAR (EasyAdmin > Ma société)',
    ];

    private const JOURS = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
    private const MOIS  = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

    /** Lieux connus : un mot-clé dans l'adresse → nom de zone (D-025). */
    private const LIEUX_CONNUS = [
        'aeroport'      => 'Aéroport Marseille Provence',
        'marignane'     => 'Aéroport Marseille Provence',
        'saint-charles' => 'Gare Saint-Charles, Marseille',
        'saint charles' => 'Gare Saint-Charles, Marseille',
        'st charles'    => 'Gare Saint-Charles, Marseille',
        'aix-en-provence tgv' => 'Gare Aix-en-Provence TGV',
        'aix tgv'       => 'Gare Aix-en-Provence TGV',
        'terminal croisiere' => 'Port de Marseille (croisières)',
        'cruise'        => 'Port de Marseille (croisières)',
    ];

    public function __construct(
        private readonly VehicleCategoryRepository $vehicleRepo,
        private readonly SiteSettingsRepository $settingsRepo,
        private readonly EntityManagerInterface $em,
        #[Autowire('%pricing.commission_percent%')]
        private readonly float $commissionPercentDefaut,
    ) {}

    public function getCommissionPercentDefaut(): float
    {
        return $this->commissionPercentDefaut;
    }

    /** Commission en € : celle enregistrée sur la réservation, sinon le taux par défaut. */
    public function commission(Reservation $r): float
    {
        return $r->getCommission() ?? round((float) $r->getPrix() * $this->commissionPercentDefaut / 100, 2);
    }

    public function prenomClient(Reservation $r): string
    {
        return trim((string) ($r->getGuestPrenom() ?: $r->getUser()?->getPrenom()));
    }

    public function telephoneClient(Reservation $r): string
    {
        return trim((string) ($r->getGuestTelephone() ?: $r->getUser()?->getTelephone()));
    }

    /** « mardi 18 novembre à 14h30 » */
    public function dateEnFrancais(?\DateTimeInterface $d): string
    {
        if (!$d) {
            return '';
        }
        return sprintf('%s %d %s à %s', self::JOURS[(int) $d->format('w')], (int) $d->format('j'), self::MOIS[(int) $d->format('n')], $d->format('G\hi'));
    }

    /**
     * Zone approximative d'une adresse (D-025) : lieu connu, sinon « code postal + ville »,
     * sinon la ville seule. Chaîne vide si on ne trouve rien (l'admin la tape).
     */
    public function zoneApproximative(?string $adresse): string
    {
        $adresse = trim((string) $adresse);
        if ($adresse === '') {
            return '';
        }

        $simple = mb_strtolower(strtr($adresse, ['é' => 'e', 'É' => 'e', 'è' => 'e', 'ô' => 'o']));
        foreach (self::LIEUX_CONNUS as $motCle => $zone) {
            if (str_contains($simple, $motCle)) {
                return $zone;
            }
        }

        // « 13008 Marseille »
        if (preg_match('/\b(\d{5})\s+([^,\d]+)/u', $adresse, $m)) {
            return $m[1] . ' ' . trim($m[2]);
        }

        // « Les Lierres, Marseille, Provence-Alpes-Côte d'Azur, France » → « Marseille »
        $parties = array_values(array_filter(
            array_map('trim', explode(',', $adresse)),
            fn(string $p) => $p !== '' && !preg_match('/^(france|provence-alpes-c[ôo]te d.azur|bouches-du-rh[ôo]ne)$/iu', $p)
        ));
        if (count($parties) >= 2) {
            return end($parties);
        }

        return '';
    }

    /**
     * Valeurs de toutes les variables pour une réservation.
     * Prix et commission sont aussi donnés en nombres (clé « _nombres ») pour le recalcul en direct.
     */
    public function variables(Reservation $r): array
    {
        $prix       = (float) $r->getPrix();
        $commission = $this->commission($r);
        $settings   = $this->settingsRepo->findCurrent();
        $societe    = $r->getEntreprise() ? $this->em->getRepository(Societe::class)->pourEntreprise($r->getEntreprise()) : null;
        $vehicule   = $r->getTypeVehicule() ? $this->vehicleRepo->findBySlug($r->getTypeVehicule()) : null;

        return [
            '{prenom}'           => $this->prenomClient($r),
            '{telephone_client}' => $this->telephoneClient($r),
            '{date_course}'      => $this->dateEnFrancais($r->getDateHeureDepart()),
            '{depart}'           => (string) $r->getDepart(),
            '{zone}'             => $this->zoneApproximative($r->getDepart()),
            '{arrivee}'          => (string) $r->getArrivee(),
            '{zone_arrivee}'     => $this->zoneApproximative($r->getArrivee()),
            '{arret}'            => $r->getStopOption() ? (string) $r->getStopLieu() : '',
            '{vehicule}'         => $vehicule?->getLabel() ?? (string) $r->getTypeVehicule(),
            '{options}'          => $r->isSiegeBebe() ? 'Siège bébé' : '',
            '{distance}'         => $r->getDistance() ? self::nombre($r->getDistance(), 1) . ' km' : '',
            '{duree}'            => $r->getDuree() ? self::duree($r->getDuree()) : '',
            '{prix}'             => self::euros($prix),
            '{mode_reglement}'   => $r->getModeReglementLabel(),
            '{commission}'       => self::euros($commission),
            '{net_chauffeur}'    => self::euros($prix - $commission),
            '{infos}'            => trim((string) $r->getInformationsComplementaires()),
            '{reference}'        => (string) $r->getId(),
            '{lien_avis_google}' => $settings?->getGoogleReviewsUrl() ?? '',
            '{telephone}'        => $societe?->getTelephone() ?? '',
        ];
    }

    /**
     * Avertissements par mode de partage quand le statut ne colle pas (D-027).
     * @return array{chauffeur: ?string, groupe: ?string, client: ?string}
     */
    public function avertissements(Reservation $r): array
    {
        $statut = ReservationStatus::tryFrom($r->getStatut());
        $label  = $statut?->label() ?? $r->getStatut();

        $chauffeur = match ($statut) {
            ReservationStatus::ANNULEE  => 'Cette course est annulée : ne la partagez pas à un chauffeur.',
            ReservationStatus::TERMINEE => 'Cette course est déjà terminée.',
            default => null,
        };

        $client = match ($statut) {
            ReservationStatus::ANNULEE  => 'Cette course est annulée.',
            ReservationStatus::TERMINEE => null,
            default => sprintf('Statut « %s » : la course n\'est pas encore terminée. Vérifiez avant d\'envoyer un remerciement.', $label),
        };
        if ($this->telephoneClient($r) === '') {
            $client = trim(($client ?? '') . ' Aucun téléphone client : choisissez le contact vous-même.');
        }

        return ['chauffeur' => $chauffeur, 'groupe' => $chauffeur, 'client' => $client];
    }

    public static function euros(float $v): string
    {
        return self::nombre($v, 2) . ' €';
    }

    private static function nombre(float $v, int $decimales): string
    {
        return number_format($v, $decimales, ',', ' ');
    }

    /** 65 → « 1 h 05 », 40 → « 40 min » */
    private static function duree(int $minutes): string
    {
        return $minutes < 60 ? $minutes . ' min' : sprintf('%d h %02d', intdiv($minutes, 60), $minutes % 60);
    }
}
