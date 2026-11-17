<?php

namespace App\Entity;

use App\Repository\MessageTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Modèle de message pour « Partager la course » (D-028), rangé dans une catégorie (D-029).
 * Modifiable dans EasyAdmin > Messages > Modèles de messages.
 * Le texte contient des variables entre accolades ({prenom}, {date_course}…) remplacées à l'écran Partager.
 */
#[ORM\Entity(repositoryClass: MessageTemplateRepository::class)]
class MessageTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private string $nom = '';

    #[ORM\ManyToOne(targetEntity: MessageCategory::class, inversedBy: 'modeles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Choisissez une catégorie.')]
    private ?MessageCategory $categorie = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $contenu = '';

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private int $ordre = 0;

    public function getId(): ?int { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getCategorie(): ?MessageCategory { return $this->categorie; }
    public function setCategorie(?MessageCategory $categorie): static { $this->categorie = $categorie; return $this; }

    public function getContenu(): string { return $this->contenu; }
    public function setContenu(string $contenu): static { $this->contenu = $contenu; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function getOrdre(): int { return $this->ordre; }
    public function setOrdre(int $ordre): static { $this->ordre = $ordre; return $this; }

    public function __toString(): string { return $this->nom; }
}
