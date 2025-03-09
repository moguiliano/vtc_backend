<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $NumeroVol = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Stop = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuArret = null;

    #[ORM\Column]
    private ?int $passagers = null;

    #[ORM\Column(nullable: true)]
    private ?int $bagages = null;

    #[ORM\Column(nullable: true)]
    private ?bool $siegeBebe = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(length: 50)]
    private ?string $TypeVehicule = null;

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

    public function getNumeroVol(): ?string
    {
        return $this->NumeroVol;
    }

    public function setNumeroVol(?string $NumeroVol): static
    {
        $this->NumeroVol = $NumeroVol;

        return $this;
    }

    public function isStop(): ?bool
    {
        return $this->Stop;
    }

    public function setStop(?bool $Stop): static
    {
        $this->Stop = $Stop;

        return $this;
    }

    public function getLieuArret(): ?string
    {
        return $this->lieuArret;
    }

    public function setLieuArret(?string $lieuArret): static
    {
        $this->lieuArret = $lieuArret;

        return $this;
    }

    public function getPassagers(): ?int
    {
        return $this->passagers;
    }

    public function setPassagers(int $passagers): static
    {
        $this->passagers = $passagers;

        return $this;
    }

    public function getBagages(): ?int
    {
        return $this->bagages;
    }

    public function setBagages(?int $bagages): static
    {
        $this->bagages = $bagages;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(?string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->TypeVehicule;
    }

    public function setTypeVehicule(string $TypeVehicule): static
    {
        $this->TypeVehicule = $TypeVehicule;

        return $this;
    }
}
