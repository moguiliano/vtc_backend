<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Limite de fréquence des inscriptions (D-075) : quelques-unes par heure et par adresse IP,
 * plus un plafond global, pour freiner les robots. Compteurs gardés 1 heure dans le cache.
 */
class LimiteInscriptions
{
    public const MAX_PAR_IP_HEURE = 3;
    public const MAX_TOTAL_HEURE = 20;

    public function __construct(private readonly CacheItemPoolInterface $cacheApp) {}

    public function estDepassee(?string $ip): bool
    {
        return $this->compteur('ip_' . hash('sha256', (string) $ip)) >= self::MAX_PAR_IP_HEURE
            || $this->compteur('total') >= self::MAX_TOTAL_HEURE;
    }

    public function compter(?string $ip): void
    {
        $this->incrementer('ip_' . hash('sha256', (string) $ip));
        $this->incrementer('total');
    }

    private function compteur(string $cle): int
    {
        $item = $this->cacheApp->getItem('inscription_' . $cle);

        return $item->isHit() ? (int) $item->get()['n'] : 0;
    }

    private function incrementer(string $cle): void
    {
        $item = $this->cacheApp->getItem('inscription_' . $cle);
        $data = $item->isHit() ? $item->get() : ['n' => 0, 'fin' => time() + 3600];
        $data['n']++;
        $item->set($data);
        $item->expiresAt((new \DateTimeImmutable())->setTimestamp($data['fin']));
        $this->cacheApp->save($item);
    }
}
