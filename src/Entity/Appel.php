<?php

namespace App\Entity;

use App\Entity\Traits\EntrepriseTrait;
use App\Repository\AppelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Journal des appels entrants : d'où vient l'appel et ce qu'il a rapporté.
 * Sert à comparer le gain apporté par Google Ads à la dépense, puis à l'import
 * des conversions dans Google Ads. Volontairement anonyme (pas de nom ni de numéro).
 */
#[ORM\Entity(repositoryClass: AppelRepository::class)]
#[ORM\Index(columns: ['date_appel'], name: 'idx_appel_date')]
class Appel implements AppartientAEntreprise
{
    /** Entreprise qui a reçu l'appel (D-020). */
    use EntrepriseTrait;

    public const SOURCES = [
        'Google Ads (annonce)'      => 'google_ads',
        'Fiche Google (Maps)'       => 'fiche_google',
        'Site dzencar.fr'           => 'site',
        'Client habituel'           => 'client_habituel',
        'Bouche-à-oreille'          => 'bouche_a_oreille',
        'Hôtel / partenaire'        => 'partenaire',
        'Autre'                     => 'autre',
        'Je ne sais pas'            => 'inconnu',
    ];

    public const TYPES = [
        'Taxi'   => 'taxi',
        'VTC'    => 'vtc',
        'Forfait VTC' => 'forfait',
    ];

    public const STATUTS = [
        'Course faite'   => 'faite',
        'Dispatchée'     => 'dispatchee',
        'Refusée'        => 'refusee',
        'Sans suite'     => 'sans_suite',
        'Annulée'        => 'annulee',
    ];

    public const MOTIFS_REFUS = [
        'Pas de chauffeur dispo'      => 'indisponible',
        'Hors zone / trop loin'       => 'hors_zone',
        'Course trop courte'          => 'trop_courte',
        'Prix jugé trop cher'         => 'prix',
        'Horaire (nuit, trop tôt…)'   => 'horaire',
        'Véhicule pas adapté'         => 'vehicule',
        'Demande pas pour nous'       => 'hors_sujet',
        'Autre'                       => 'autre',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Jour et heure de l'appel */
    #[ORM\Column]
    private \DateTimeImmutable $dateAppel;

    /** Jour et heure de la course (si différente de l'appel : réservation à l'avance) */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateCourse = null;

    #[ORM\Column(length: 30)]
    private string $source = 'google_ads';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $typeService = null;

    #[ORM\Column(length: 20)]
    private string $statut = 'faite';

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $motifRefus = null;

    /** Trajet en clair, ex. « Aéroport → Vieux-Port » */
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $trajet = null;

    /** Prix payé par le client */
    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $prixCourse = null;

    /** Ce que je gagne vraiment : le prix si je fais la course, la commission si je dispatche */
    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $gain = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->dateAppel = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getDateAppel(): \DateTimeImmutable { return $this->dateAppel; }
    public function setDateAppel(\DateTimeImmutable $dateAppel): static { $this->dateAppel = $dateAppel; return $this; }

    public function getDateCourse(): ?\DateTimeImmutable { return $this->dateCourse; }
    public function setDateCourse(?\DateTimeImmutable $dateCourse): static { $this->dateCourse = $dateCourse; return $this; }

    public function getSource(): string { return $this->source; }
    public function setSource(string $source): static { $this->source = $source; return $this; }

    public function getTypeService(): ?string { return $this->typeService; }
    public function setTypeService(?string $typeService): static { $this->typeService = $typeService; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getMotifRefus(): ?string { return $this->motifRefus; }
    public function setMotifRefus(?string $motifRefus): static { $this->motifRefus = $motifRefus; return $this; }

    public function getTrajet(): ?string { return $this->trajet; }
    public function setTrajet(?string $trajet): static { $this->trajet = $trajet; return $this; }

    public function getPrixCourse(): ?string { return $this->prixCourse; }
    public function setPrixCourse(?string $prixCourse): static { $this->prixCourse = $prixCourse; return $this; }

    public function getGain(): ?string { return $this->gain; }
    public function setGain(?string $gain): static { $this->gain = $gain; return $this; }

    public function getObservation(): ?string { return $this->observation; }
    public function setObservation(?string $observation): static { $this->observation = $observation; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
