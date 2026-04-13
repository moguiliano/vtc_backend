<?php

namespace App\Entity;

use App\Repository\VehicleCategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleCategoryRepository::class)]
#[ORM\Table(name: 'vehicle_category')]
class VehicleCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Identifiant technique : eco_berline, grand_coffre, berline, van */
    #[ORM\Column(length: 50, unique: true)]
    private string $slug;

    /** Nom affiché : "Eco-Berline", "Grand Coffre", etc. */
    #[ORM\Column(length: 100)]
    private string $label;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /** Prix de base si distance <= thresholdKm */
    #[ORM\Column]
    private float $basePriceUnderThreshold;

    /** Prix de base si distance > thresholdKm */
    #[ORM\Column]
    private float $basePriceOverThreshold;

    /** Prix par km supplémentaire si distance <= thresholdKm */
    #[ORM\Column]
    private float $pricePerKmUnderThreshold;

    /** Prix par km supplémentaire si distance > thresholdKm */
    #[ORM\Column]
    private float $pricePerKmOverThreshold;

    /** Seuil en km déclenchant la 2e formule */
    #[ORM\Column]
    private float $thresholdKm;

    #[ORM\Column]
    private int $maxPassengers = 4;

    #[ORM\Column]
    private int $luggageCapacity = 2;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilename = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private int $displayOrder = 0;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // -------------------------------------------------------------------------
    // Logique métier
    // -------------------------------------------------------------------------

    /**
     * Calcule le prix brut selon la distance.
     * La majoration nuit et la commission sont gérées par HereMapsService.
     */
    public function calculerPrixBrut(float $distance): float
    {
        if ($distance <= $this->thresholdKm) {
            return $this->basePriceUnderThreshold + ($distance * $this->pricePerKmUnderThreshold);
        }

        return $this->basePriceOverThreshold + (($distance - $this->thresholdKm) * $this->pricePerKmOverThreshold);
    }

    public function __toString(): string
    {
        return $this->label;
    }

    // -------------------------------------------------------------------------
    // Getters / Setters
    // -------------------------------------------------------------------------

    public function getId(): ?int { return $this->id; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): static { $this->slug = $slug; return $this; }

    public function getLabel(): string { return $this->label; }
    public function setLabel(string $label): static { $this->label = $label; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getBasePriceUnderThreshold(): float { return $this->basePriceUnderThreshold; }
    public function setBasePriceUnderThreshold(float $v): static { $this->basePriceUnderThreshold = $v; return $this; }

    public function getBasePriceOverThreshold(): float { return $this->basePriceOverThreshold; }
    public function setBasePriceOverThreshold(float $v): static { $this->basePriceOverThreshold = $v; return $this; }

    public function getPricePerKmUnderThreshold(): float { return $this->pricePerKmUnderThreshold; }
    public function setPricePerKmUnderThreshold(float $v): static { $this->pricePerKmUnderThreshold = $v; return $this; }

    public function getPricePerKmOverThreshold(): float { return $this->pricePerKmOverThreshold; }
    public function setPricePerKmOverThreshold(float $v): static { $this->pricePerKmOverThreshold = $v; return $this; }

    public function getThresholdKm(): float { return $this->thresholdKm; }
    public function setThresholdKm(float $v): static { $this->thresholdKm = $v; return $this; }

    public function getMaxPassengers(): int { return $this->maxPassengers; }
    public function setMaxPassengers(int $v): static { $this->maxPassengers = $v; return $this; }

    public function getLuggageCapacity(): int { return $this->luggageCapacity; }
    public function setLuggageCapacity(int $v): static { $this->luggageCapacity = $v; return $this; }

    public function getImageFilename(): ?string { return $this->imageFilename; }
    public function setImageFilename(?string $v): static { $this->imageFilename = $v; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): static { $this->isActive = $v; return $this; }

    public function getDisplayOrder(): int { return $this->displayOrder; }
    public function setDisplayOrder(int $v): static { $this->displayOrder = $v; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $v): static { $this->updatedAt = $v; return $this; }
}
