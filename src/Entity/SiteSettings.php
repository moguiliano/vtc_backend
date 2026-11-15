<?php

namespace App\Entity;

use App\Repository\SiteSettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Réglages du site modifiables depuis EasyAdmin (une seule ligne en base).
 * Pour l'instant : la note et le nombre d'avis de la fiche Google (décision D-009).
 */
#[ORM\Entity(repositoryClass: SiteSettingsRepository::class)]
class SiteSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 2, scale: 1)]
    #[Assert\Range(min: 1, max: 5)]
    private string $googleRating = '5.0';

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private int $googleReviewCount = 0;

    #[ORM\Column(length: 255)]
    #[Assert\Url]
    private string $googleReviewsUrl = 'https://share.google/5lmVFiBFJ5YK0O7ds';

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getGoogleRating(): string { return $this->googleRating; }
    public function setGoogleRating(string $googleRating): static { $this->googleRating = $googleRating; $this->touch(); return $this; }

    public function getGoogleReviewCount(): int { return $this->googleReviewCount; }
    public function setGoogleReviewCount(int $googleReviewCount): static { $this->googleReviewCount = $googleReviewCount; $this->touch(); return $this; }

    public function getGoogleReviewsUrl(): string { return $this->googleReviewsUrl; }
    public function setGoogleReviewsUrl(string $googleReviewsUrl): static { $this->googleReviewsUrl = $googleReviewsUrl; $this->touch(); return $this; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
