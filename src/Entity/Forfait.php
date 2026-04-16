<?php

namespace App\Entity;

use App\Repository\ForfaitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForfaitRepository::class)]
class Forfait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $depart = null;

    #[ORM\Column(length: 100)]
    private ?string $arrivee = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private int $ordre = 0;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $icone = null;

    public function getId(): ?int { return $this->id; }

    public function getDepart(): ?string { return $this->depart; }
    public function setDepart(string $depart): static { $this->depart = $depart; return $this; }

    public function getArrivee(): ?string { return $this->arrivee; }
    public function setArrivee(string $arrivee): static { $this->arrivee = $arrivee; return $this; }

    public function getPrix(): ?int { return $this->prix; }
    public function setPrix(int $prix): static { $this->prix = $prix; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function getOrdre(): int { return $this->ordre; }
    public function setOrdre(int $ordre): static { $this->ordre = $ordre; return $this; }

    public function getIcone(): ?string { return $this->icone; }
    public function setIcone(?string $icone): static { $this->icone = $icone; return $this; }
}
