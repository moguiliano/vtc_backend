<?php

namespace App\Service;

use App\Entity\Entreprise;
use App\Entity\GrilleTarif;
use App\Entity\VehicleCategory;
use App\Repository\GrilleTarifRepository;

/**
 * Brique « Calculer un prix » (D-020).
 *
 * Lit la grille de prix de l'entreprise (D-016) ; sans entreprise précisée : l'entreprise courante.
 * Majoration de nuit et commission : paramètres de config/services.yaml (D-006, à trancher).
 * HereMapsService ne fait plus que les cartes (adresses, distance, durée).
 */
class Tarification
{
    /** @var array<int, GrilleTarif[]> grilles proposées, par id d'entreprise (cache de la requête) */
    private array $cache = [];

    public function __construct(
        private readonly GrilleTarifRepository $grilleRepo,
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly float $commissionPercent = 20.0,
        private readonly float $nightSurchargePercent = 20.0,
        private readonly int $nightStartHour = 23,
        private readonly int $nightEndHour = 7,
    ) {}

    /** @return GrilleTarif[] grilles proposées par l'entreprise, triées (displayOrder) */
    public function grilles(?Entreprise $entreprise = null): array
    {
        $entreprise ??= $this->entrepriseCourante->get();

        return $this->cache[$entreprise->getId()] ??= $this->grilleRepo->findProposees($entreprise);
    }

    /** @return VehicleCategory[] catégories proposées par l'entreprise (pour l'onglet 2 et ses contrôles) */
    public function categoriesProposees(?Entreprise $entreprise = null): array
    {
        return array_map(fn (GrilleTarif $g) => $g->getCategorie(), $this->grilles($entreprise));
    }

    public function grille(string $slug, ?Entreprise $entreprise = null): ?GrilleTarif
    {
        foreach ($this->grilles($entreprise) as $grille) {
            if ($grille->getCategorie()->getSlug() === $slug) {
                return $grille;
            }
        }

        return null;
    }

    /** Prix « À partir de » affiché sur la carte du véhicule (onglet 2). */
    public function prixDepart(VehicleCategory $categorie, ?Entreprise $entreprise = null): ?float
    {
        return $this->grille($categorie->getSlug(), $entreprise)?->getBasePriceUnderThreshold();
    }

    /** Estimation pour une catégorie ; null si l'entreprise ne la propose pas. */
    public function estimerPrix(float $distance, string $slug, int $heure, ?Entreprise $entreprise = null): ?array
    {
        $grille = $this->grille($slug, $entreprise);

        return $grille ? $this->estimer($grille, $distance, $heure) : null;
    }

    /**
     * Estimations de toutes les catégories proposées, par slug.
     * @return array<string, array{prix_total: float, commission: float, net_chauffeur: float, majoration_nuit: bool}>
     */
    public function estimerToutesCategories(float $distance, int $heure, ?Entreprise $entreprise = null): array
    {
        $result = [];
        foreach ($this->grilles($entreprise) as $grille) {
            $result[$grille->getCategorie()->getSlug()] = $this->estimer($grille, $distance, $heure);
        }

        return $result;
    }

    public function estNuit(int $heure): bool
    {
        return $heure < $this->nightEndHour || $heure >= $this->nightStartHour;
    }

    private function estimer(GrilleTarif $grille, float $distance, int $heure): array
    {
        $prix   = $grille->calculerPrixBrut($distance);
        $isNuit = $this->estNuit($heure);

        if ($isNuit) {
            $prix = round($prix * (1 + $this->nightSurchargePercent / 100), 2);
        }

        $prix       = round($prix, 2);
        $commission = round($prix * ($this->commissionPercent / 100), 2);

        return [
            'prix_total'      => $prix,
            'commission'      => $commission,
            'net_chauffeur'   => round($prix - $commission, 2),
            'majoration_nuit' => $isNuit,
        ];
    }
}
