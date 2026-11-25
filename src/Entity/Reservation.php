<?php
namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Traits\EntrepriseTrait;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation implements AppartientAEntreprise
{
    /** Entreprise propriétaire de la réservation (D-020). */
    use EntrepriseTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $depart = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $arrivee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureDepart = null;

    #[ORM\Column(nullable: true)]
    private ?bool $stopOption = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $stopLieu = null;

    #[ORM\Column(nullable: true)]
    private ?bool $siegeBebe = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $distance = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duree = 0;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $typeVehicule = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $prix = 0.0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    /** Commission en € (D-024). Null tant que l'admin ne l'a pas fixée au partage. */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $commission = null;

    /** Chauffeur retenu, texte libre (ex. « Karim — 06 12 34 56 78 »), D-067. */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chauffeurRetenu = null;

    /**
     * Abonné validé qui fait la course (D-079 A, D-083), facultatif.
     * La réservation reste à SIMPLICI CAB : on note seulement qui roule.
     */
    #[ORM\ManyToOne(targetEntity: Entreprise::class)]
    #[ORM\JoinColumn(name: 'abonne_retenu_id', nullable: true, onDelete: 'SET NULL')]
    private ?Entreprise $abonneRetenu = null;

    /** Statut côté chauffeur de l'abonné retenu (D-102, D-106) ; null = proposée, pas encore de réponse. */
    #[ORM\Column(length: 20, nullable: true, enumType: \App\Enum\StatutChauffeur::class)]
    private ?\App\Enum\StatutChauffeur $statutChauffeur = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $statutChauffeurLe = null;

    /** Dernière modification : qui (email du compte admin, ou « site ») et quand (D-068). */
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $modifiePar = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modifieLe = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isGuest = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $guestInfo = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $guestPrenom = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $guestTelephone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $informationsComplementaires = null;

    #[ORM\Column(length: 20)]
    private string $modeReglement = 'carte_bancaire';

    #[ORM\Column(length: 30)]
    private string $statut = 'en_attente';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- Getters and setters ---
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepart(): ?string
    {
        return $this->depart;
    }

    public function setDepart(string $depart): static
    {
        $this->depart = $depart;
        return $this;
    }

    public function getArrivee(): ?string
    {
        return $this->arrivee;
    }

    public function setArrivee(string $arrivee): static
    {
        $this->arrivee = $arrivee;
        return $this;
    }

    public function getDateHeureDepart(): ?\DateTimeInterface
    {
        return $this->dateHeureDepart;
    }

    public function setDateHeureDepart(\DateTimeInterface $dateHeureDepart): static
    {
        $this->dateHeureDepart = $dateHeureDepart;
        return $this;
    }

    public function getStopOption(): ?bool
    {
        return $this->stopOption;
    }

    public function setStopOption(?bool $stopOption): static
    {
        $this->stopOption = $stopOption;
        return $this;
    }

    public function getStopLieu(): ?string
    {
        return $this->stopLieu;
    }

    public function setStopLieu(?string $stopLieu): static
    {
        $this->stopLieu = $stopLieu;
        return $this;
    }

    public function isSiegeBebe(): ?bool
    {
        return $this->siegeBebe;
    }

    public function setSiegeBebe(?bool $siegeBebe): static
    {
        $this->siegeBebe = $siegeBebe;
        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(?float $distance): static
    {
        $this->distance = $distance;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;
        return $this;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->typeVehicule;
    }

    public function setTypeVehicule(string $typeVehicule): static
    {
        $this->typeVehicule = $typeVehicule;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getIsGuest(): bool
    {
        return $this->isGuest;
    }

    public function setIsGuest(bool $isGuest): static
    {
        $this->isGuest = $isGuest;
        return $this;
    }

    public function getGuestInfo(): ?string
    {
        return $this->guestInfo;
    }

    public function setGuestInfo(?string $guestInfo): static
    {
        $this->guestInfo = $guestInfo;
        return $this;
    }

    public function getCommission(): ?float
    {
        return $this->commission;
    }

    /** Commission en € enregistrée au partage (D-024) ; null = taux par défaut (services.yaml). */
    public function setCommission(?float $commission): static
    {
        $this->commission = $commission;
        return $this;
    }

    public function getChauffeurRetenu(): ?string
    {
        return $this->chauffeurRetenu;
    }

    public function setChauffeurRetenu(?string $chauffeurRetenu): static
    {
        $chauffeurRetenu = $chauffeurRetenu !== null ? trim($chauffeurRetenu) : null;
        $this->chauffeurRetenu = $chauffeurRetenu === '' ? null : $chauffeurRetenu;
        return $this;
    }

    public function getAbonneRetenu(): ?Entreprise { return $this->abonneRetenu; }
    /** Un autre abonné (ou aucun) : son statut chauffeur repart de zéro (D-100, D-107). */
    public function setAbonneRetenu(?Entreprise $abonne): static
    {
        if ($abonne?->getId() !== $this->abonneRetenu?->getId()) {
            $this->statutChauffeur = null;
            $this->statutChauffeurLe = null;
        }
        $this->abonneRetenu = $abonne;
        return $this;
    }

    public function getStatutChauffeur(): ?\App\Enum\StatutChauffeur { return $this->statutChauffeur; }
    public function getStatutChauffeurLe(): ?\DateTimeImmutable { return $this->statutChauffeurLe; }

    /** Réponse du chauffeur retenu : statut chauffeur + statut client synchronisé (D-106). */
    public function repondreChauffeur(\App\Enum\StatutChauffeur $s, \DateTimeImmutable $quand): static
    {
        $this->statutChauffeur = $s;
        $this->statutChauffeurLe = $quand;
        $this->statut = $s->statutClient()->value;
        return $this;
    }

    /** Colonne « Chauffeur » de l'admin (D-107) : « Karim VTC — A pris la course ». */
    public function getResumeChauffeur(): ?string
    {
        if (!$this->abonneRetenu) {
            return null;
        }
        return $this->abonneRetenu->getNom() . ' — ' . ($this->statutChauffeur?->label() ?? 'Proposée');
    }

    public function getModifiePar(): ?string
    {
        return $this->modifiePar;
    }

    public function getModifieLe(): ?\DateTimeImmutable
    {
        return $this->modifieLe;
    }

    /** Appelé automatiquement à chaque modification (ReservationModificationListener). */
    public function marquerModifiee(string $par, \DateTimeImmutable $le): static
    {
        $this->modifiePar = $par;
        $this->modifieLe  = $le;
        return $this;
    }

    public function getGuestPrenom(): ?string
    {
        return $this->guestPrenom;
    }

    public function setGuestPrenom(?string $guestPrenom): static
    {
        $this->guestPrenom = $guestPrenom;
        return $this;
    }

    public function getGuestTelephone(): ?string
    {
        return $this->guestTelephone;
    }

    public function setGuestTelephone(?string $guestTelephone): static
    {
        $this->guestTelephone = $guestTelephone;
        return $this;
    }

    public function getInformationsComplementaires(): ?string
    {
        return $this->informationsComplementaires;
    }

    public function setInformationsComplementaires(?string $informationsComplementaires): static
    {
        $this->informationsComplementaires = $informationsComplementaires;
        return $this;
    }

    public function getModeReglement(): string
    {
        return $this->modeReglement;
    }

    public function setModeReglement(string $modeReglement): static
    {
        $this->modeReglement = $modeReglement;
        return $this;
    }

    public function getModeReglementLabel(): string
    {
        return match ($this->modeReglement) {
            'especes'       => 'Espèces',
            'carte_bancaire'=> 'Carte bancaire',
            default         => $this->modeReglement,
        };
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt instanceof \DateTimeImmutable
            ? $createdAt
            : \DateTimeImmutable::createFromMutable($createdAt);
        return $this;
    }
}
