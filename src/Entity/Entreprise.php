<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Le « propriétaire » des données (D-020, étape 0 : fondation).
 *
 * SIMPLICI CAB = entreprise n°1, la seule pour l'instant (créée par migration).
 * Plus tard : un abonné = une entreprise, avec son état (en attente / actif / lecture seule, D-014, D-015).
 */
#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $nom = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function __toString(): string { return $this->nom; }
}
