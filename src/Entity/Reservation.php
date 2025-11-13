<?php
namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
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

    #[ORM\Column(type: 'integer' ,nullable: true)]
    private int $duree;

    #[ORM\Column(length: 50)]
    private ?string $typeVehicule = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private float $prix;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isGuest = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $guestInfo = null;

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

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): static
    {
        $this->distance = $distance;
        return $this;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
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

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
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
}
