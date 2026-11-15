<?php

namespace App\Twig;

use App\Entity\SiteSettings;
use App\Repository\SiteSettingsRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Expose les réglages du site aux templates : {{ site_settings().googleReviewCount }}.
 * Renvoie null si la table est vide (le template prévoit alors un affichage sans chiffres).
 */
class SiteSettingsExtension extends AbstractExtension
{
    private ?SiteSettings $cache = null;
    private bool $loaded = false;

    public function __construct(private readonly SiteSettingsRepository $repository)
    {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('site_settings', [$this, 'getSettings'])];
    }

    public function getSettings(): ?SiteSettings
    {
        if (!$this->loaded) {
            $this->cache = $this->repository->findCurrent();
            $this->loaded = true;
        }

        return $this->cache;
    }
}
