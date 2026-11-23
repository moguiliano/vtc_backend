<?php

namespace App\Entity;

use App\Entity\Traits\EntrepriseTrait;
use App\Repository\GrilleTarifRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Prix d'une catégorie de véhicule pour une entreprise (D-016).
 *
 * Les catégories (VehicleCategory) sont communes à tout le monde ; chaque entreprise
 * coche celles qu'elle propose (`actif`) et fixe ses propres prix ici.
 * La majoration de nuit et la commission sont appliquées par le service Tarification.
 */
#[ORM\Entity(repositoryClass: GrilleTarifRepository::class)]
#[ORM\Table(name: 'grille_tarif')]
#[ORM\UniqueConstraint(name: 'uniq_grille_entreprise_categorie', columns: ['entreprise_id', 'categorie_id'])]
class GrilleTarif implements AppartientAEntreprise
{
    use EntrepriseTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: VehicleCategory::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?VehicleCategory $categorie = null;

    /** Catégorie proposée par l'entreprise (cochée) */
    #[ORM\Column]
    private bool $actif = true;

    /** Seuil en km déclenchant la 2e formule */
    #[ORM\Column]
    private float $thresholdKm = 0.0;

    /** Prix de base si distance <= thresholdKm */
    #[ORM\Column]
    private float $basePriceUnderThreshold = 0.0;

    /** Prix par km si distance <= thresholdKm */
    #[ORM\Column]
    private float $pricePerKmUnderThreshold = 0.0;

    /** Prix de base si distance > thresholdKm */
    #[ORM\Column]
    private float $basePriceOverThreshold = 0.0;

    /** Prix par km au-delà du seuil si distance > thresholdKm */
    #[ORM\Column]
    private float $pricePerKmOverThreshold = 0.0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /** Prix brut selon la distance (avant majoration de nuit). */
    public function calculerPrixBrut(float $distance): float
    {
        if ($distance <= $this->thresholdKm) {
            return $this->basePriceUnderThreshold + ($distance * $this->pricePerKmUnderThreshold);
        }

        return $this->basePriceOverThreshold + (($distance - $this->thresholdKm) * $this->pricePerKmOverThreshold);
    }

    public function __toString(): string
    {
        return (string) $this->categorie?->getLabel();
    }

    public function getId(): ?int { return $this->id; }

    public function getCategorie(): ?VehicleCategory { return $this->categorie; }
    public function setCategorie(VehicleCategory $categorie): static { $this->categorie = $categorie; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function getThresholdKm(): float { return $this->thresholdKm; }
    public function setThresholdKm(float $v): static { $this->thresholdKm = $v; return $this; }

    public function getBasePriceUnderThreshold(): float { return $this->basePriceUnderThreshold; }
    public function setBasePriceUnderThreshold(float $v): static { $this->basePriceUnderThreshold = $v; return $this; }

    public function getPricePerKmUnderThreshold(): float { return $this->pricePerKmUnderThreshold; }
    public function setPricePerKmUnderThreshold(float $v): static { $this->pricePerKmUnderThreshold = $v; return $this; }

    public function getBasePriceOverThreshold(): float { return $this->basePriceOverThreshold; }
    public function setBasePriceOverThreshold(float $v): static { $this->basePriceOverThreshold = $v; return $this; }

    public function getPricePerKmOverThreshold(): float { return $this->pricePerKmOverThreshold; }
    public function setPricePerKmOverThreshold(float $v): static { $this->pricePerKmOverThreshold = $v; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $v): static { $this->updatedAt = $v; return $this; }
}
