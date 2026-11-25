<?php

namespace App\Entity;

use App\Enum\EtatAbonne;
use App\Enum\TypeAbonnement;
use App\Repository\EntrepriseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Le « propriétaire » des données (D-020, étape 0 : fondation).
 *
 * SIMPLICI CAB = entreprise n°1, la seule pour l'instant (créée par migration).
 * Un abonné = une entreprise, avec son type et son état (D-014, D-015, D-022, D-063).
 * Les entreprises abonnées ne sont JAMAIS l'entreprise n°1.
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

    /** Chauffeur indépendant ou dispatcheur ; null pour SIMPLICI CAB (pas un abonné). */
    #[ORM\Column(length: 20, nullable: true, enumType: TypeAbonnement::class)]
    private ?TypeAbonnement $typeAbonnement = null;

    #[ORM\Column(length: 20, enumType: EtatAbonne::class)]
    private EtatAbonne $etat = EtatAbonne::EN_ATTENTE;

    /** Jour de la validation de la société par le super-admin = début du mois d'essai (D-077). */
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $essaiDebut = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getTypeAbonnement(): ?TypeAbonnement { return $this->typeAbonnement; }
    public function setTypeAbonnement(?TypeAbonnement $type): static { $this->typeAbonnement = $type; return $this; }

    public function getEtat(): EtatAbonne { return $this->etat; }
    public function setEtat(EtatAbonne $etat): static { $this->etat = $etat; return $this; }

    public function getEssaiDebut(): ?\DateTimeImmutable { return $this->essaiDebut; }
    public function setEssaiDebut(?\DateTimeInterface $d): static
    {
        $this->essaiDebut = $d ? \DateTimeImmutable::createFromInterface($d) : null;
        return $this;
    }

    /** Dernier jour d'essai inclus : début + 1 mois - 1 jour (D-022, D-077). */
    public function getEssaiFin(): ?\DateTimeImmutable
    {
        return $this->essaiDebut?->modify('+1 month')->modify('-1 day');
    }

    public function isEnAttente(): bool { return $this->etat === EtatAbonne::EN_ATTENTE; }

    /** Validation de la société par le super-admin : l'essai démarre aujourd'hui (D-015, D-077). */
    public function valider(\DateTimeImmutable $aujourdhui = new \DateTimeImmutable('today')): static
    {
        if (!$this->isEnAttente()) {
            throw new \LogicException('Seul un abonné en attente peut être validé.');
        }
        $this->etat = EtatAbonne::ESSAI;
        $this->essaiDebut = $aujourdhui;

        return $this;
    }

    public function __toString(): string { return $this->nom; }
}
