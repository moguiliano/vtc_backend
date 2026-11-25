<?php

namespace App\Service;

use App\Entity\Entreprise;
use App\Entity\GrilleTarif;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Grilles de prix des abonnés (D-087, D-090, D-091).
 *
 * - À la validation : une ligne par catégorie, prix copiés de SIMPLICI CAB, rien de coché, « comme ZenCAR ».
 * - Quand SIMPLICI CAB change un prix : les lignes « comme ZenCAR » des abonnés suivent.
 * - « Revenir aux prix ZenCAR » : l'abonné se raccroche.
 *
 * Lectures / écritures en SQL direct (DBAL) : le tri automatique par entreprise (EntrepriseFilter)
 * ne s'applique qu'à Doctrine ORM. On ne lit ici que les PRIX de SIMPLICI CAB (déjà publics via le
 * calculateur du site) et on n'écrit que dans les grilles des abonnés.
 */
class GrilleAbonnes
{
    private const COLONNES = 'threshold_km, base_price_under_threshold, price_per_km_under_threshold, price_per_km_over_threshold';

    public function __construct(
        private readonly Connection $db,
        private readonly EntityManagerInterface $em,
    ) {}

    /** Prix de SIMPLICI CAB, par id de catégorie : [seuil, prise en charge, €/km ≤ seuil, €/km > seuil]. */
    public function prixZencar(): array
    {
        $prix = [];
        foreach ($this->db->fetchAllAssociative(
            'SELECT categorie_id, ' . self::COLONNES . ' FROM grille_tarif WHERE entreprise_id = ?',
            [EntrepriseCourante::ID_PRINCIPALE]
        ) as $l) {
            $prix[(int) $l['categorie_id']] = [(float) $l['threshold_km'], (float) $l['base_price_under_threshold'],
                (float) $l['price_per_km_under_threshold'], (float) $l['price_per_km_over_threshold']];
        }

        return $prix;
    }

    /** À la validation (D-087) : crée les lignes manquantes de l'abonné, copiées de SIMPLICI CAB. */
    public function creerPour(Entreprise $abonne): void
    {
        if (!$abonne->estAbonne()) {
            return;
        }
        $existantes = array_map('intval', $this->db->fetchFirstColumn(
            'SELECT categorie_id FROM grille_tarif WHERE entreprise_id = ?', [$abonne->getId()]
        ));
        foreach ($this->prixZencar() as $categorieId => [$seuil, $pec, $kmSous, $kmSur]) {
            if (in_array($categorieId, $existantes, true)) {
                continue;
            }
            $this->db->insert('grille_tarif', [
                'entreprise_id' => $abonne->getId(),
                'categorie_id' => $categorieId,
                'actif' => 0,                         // aucun véhicule coché au départ
                'suit_zencar' => 1,
                'threshold_km' => $seuil,
                'base_price_under_threshold' => $pec,
                'price_per_km_under_threshold' => $kmSous,
                'base_price_over_threshold' => round($pec + $seuil * $kmSous, 2),
                'price_per_km_over_threshold' => $kmSur,
            ]);
        }
    }

    /**
     * SIMPLICI CAB vient de modifier ou d'ajouter une ligne (D-091) : les abonnés « comme ZenCAR » suivent,
     * et les abonnés validés qui n'ont pas encore cette catégorie la reçoivent (non cochée).
     */
    public function propagerDepuis(GrilleTarif $grilleZencar): int
    {
        if ($grilleZencar->getEntreprise()?->getId() !== EntrepriseCourante::ID_PRINCIPALE) {
            return 0;
        }
        [$seuil, $pec, $kmSous, $kmSur] = $grilleZencar->prix();
        $categorieId = $grilleZencar->getCategorie()->getId();

        $n = $this->db->executeStatement(
            'UPDATE grille_tarif SET threshold_km = ?, base_price_under_threshold = ?, price_per_km_under_threshold = ?,
                base_price_over_threshold = ?, price_per_km_over_threshold = ?
             WHERE categorie_id = ? AND entreprise_id <> ? AND suit_zencar = 1',
            [$seuil, $pec, $kmSous, round($pec + $seuil * $kmSous, 2), $kmSur, $categorieId, EntrepriseCourante::ID_PRINCIPALE]
        );

        foreach ($this->db->fetchFirstColumn(
            "SELECT e.id FROM entreprise e WHERE e.id <> ? AND e.etat <> 'en_attente'
               AND NOT EXISTS (SELECT 1 FROM grille_tarif g WHERE g.entreprise_id = e.id AND g.categorie_id = ?)",
            [EntrepriseCourante::ID_PRINCIPALE, $categorieId]
        ) as $id) {
            $this->db->insert('grille_tarif', [
                'entreprise_id' => (int) $id, 'categorie_id' => $categorieId, 'actif' => 0, 'suit_zencar' => 1,
                'threshold_km' => $seuil, 'base_price_under_threshold' => $pec, 'price_per_km_under_threshold' => $kmSous,
                'base_price_over_threshold' => round($pec + $seuil * $kmSous, 2), 'price_per_km_over_threshold' => $kmSur,
            ]);
            $n++;
        }

        return $n;
    }

    /** « Revenir aux prix ZenCAR » (D-091), pour une ligne de l'abonné courant. */
    public function raccrocher(GrilleTarif $grille): void
    {
        $zencar = $this->prixZencar()[$grille->getCategorie()->getId()] ?? null;
        if ($zencar === null) {
            return;
        }
        $grille->setPrix(...$zencar)->setSuitZencar(true)->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }
}
