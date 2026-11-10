<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $dateFacture;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $dateTransaction;

    #[ORM\Column(length: 150)]
    private ?string $clientNom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $clientAdresse = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $clientSiret = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $clientTva = null;

    #[ORM\Column(length: 255)]
    private string $description = 'Prix du service de transport';

    #[ORM\Column(type: 'float')]
    private ?float $prixTtc = null;

    #[ORM\Column(type: 'float')]
    private float $tauxTva = 10.0;

    #[ORM\Column(length: 20)]
    private string $modeReglement = 'carte_bancaire';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Reservation $reservation = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->dateFacture     = $now;
        $this->dateTransaction = $now;
        $this->createdAt       = $now;
    }

    public function getId(): ?int { return $this->id; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = $numero; return $this; }

    public function getDateFacture(): \DateTimeImmutable { return $this->dateFacture; }
    public function setDateFacture(\DateTimeInterface $d): static { $this->dateFacture = \DateTimeImmutable::createFromInterface($d); return $this; }

    public function getDateTransaction(): \DateTimeImmutable { return $this->dateTransaction; }
    public function setDateTransaction(\DateTimeInterface $d): static { $this->dateTransaction = \DateTimeImmutable::createFromInterface($d); return $this; }

    public function getClientNom(): ?string { return $this->clientNom; }
    public function setClientNom(string $clientNom): static { $this->clientNom = $clientNom; return $this; }

    public function getClientAdresse(): ?string { return $this->clientAdresse; }
    public function setClientAdresse(?string $clientAdresse): static { $this->clientAdresse = $clientAdresse; return $this; }

    public function getClientSiret(): ?string { return $this->clientSiret; }
    public function setClientSiret(?string $clientSiret): static { $this->clientSiret = $clientSiret; return $this; }

    public function getClientTva(): ?string { return $this->clientTva; }
    public function setClientTva(?string $clientTva): static { $this->clientTva = $clientTva; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getPrixTtc(): ?float { return $this->prixTtc; }
    public function setPrixTtc(?float $prixTtc): static { $this->prixTtc = $prixTtc; return $this; }

    public function getTauxTva(): float { return $this->tauxTva; }
    public function setTauxTva(float $tauxTva): static { $this->tauxTva = $tauxTva; return $this; }

    public function getModeReglement(): string { return $this->modeReglement; }
    public function setModeReglement(string $modeReglement): static { $this->modeReglement = $modeReglement; return $this; }

    public function getReservation(): ?Reservation { return $this->reservation; }
    public function setReservation(?Reservation $reservation): static { $this->reservation = $reservation; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getMontantHt(): float
    {
        return round(($this->prixTtc ?? 0.0) / (1 + $this->tauxTva / 100), 2);
    }

    public function getMontantTva(): float
    {
        return round(($this->prixTtc ?? 0.0) - $this->getMontantHt(), 2);
    }
}
