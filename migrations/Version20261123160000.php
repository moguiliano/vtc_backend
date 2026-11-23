<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fondation (D-020) — 7/7 : vehicle_category ne garde que ce qui est commun (D-016).
 * Ses colonnes de prix ont été copiées dans grille_tarif (5/7) : on les retire.
 * down() les recrée et les remplit depuis la grille de l'entreprise n°1.
 */
final class Version20261123160000 extends AbstractMigration
{
    private const COLONNES = [
        'base_price_under_threshold',
        'base_price_over_threshold',
        'price_per_km_under_threshold',
        'price_per_km_over_threshold',
        'threshold_km',
    ];

    public function getDescription(): string
    {
        return 'Fondation 7/7 : retrait des colonnes de prix de vehicle_category (déjà copiées dans grille_tarif)';
    }

    /** MariaDB/MySQL valident chaque ALTER TABLE tout de suite : pas de transaction possible. */
    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        // Sécurité : chaque catégorie doit avoir sa grille SIMPLICI CAB avant de supprimer ses prix
        $sansGrille = (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM vehicle_category vc LEFT JOIN grille_tarif g ON g.categorie_id = vc.id AND g.entreprise_id = 1 WHERE g.id IS NULL'
        );
        $this->abortIf($sansGrille > 0, "$sansGrille catégorie(s) sans grille de prix pour l'entreprise 1 : rien n'a été supprimé.");

        $this->addSql('ALTER TABLE vehicle_category DROP ' . implode(', DROP ', self::COLONNES));
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vehicle_category
            ADD base_price_under_threshold DOUBLE PRECISION DEFAULT NULL AFTER description,
            ADD base_price_over_threshold DOUBLE PRECISION DEFAULT NULL AFTER base_price_under_threshold,
            ADD price_per_km_under_threshold DOUBLE PRECISION DEFAULT NULL AFTER base_price_over_threshold,
            ADD price_per_km_over_threshold DOUBLE PRECISION DEFAULT NULL AFTER price_per_km_under_threshold,
            ADD threshold_km DOUBLE PRECISION DEFAULT NULL AFTER price_per_km_over_threshold');
        $this->addSql('UPDATE vehicle_category vc LEFT JOIN grille_tarif g ON g.categorie_id = vc.id AND g.entreprise_id = 1
            SET vc.base_price_under_threshold   = COALESCE(g.base_price_under_threshold, 0),
                vc.base_price_over_threshold    = COALESCE(g.base_price_over_threshold, 0),
                vc.price_per_km_under_threshold = COALESCE(g.price_per_km_under_threshold, 0),
                vc.price_per_km_over_threshold  = COALESCE(g.price_per_km_over_threshold, 0),
                vc.threshold_km                 = COALESCE(g.threshold_km, 0)');
        foreach (self::COLONNES as $colonne) {
            $this->addSql("ALTER TABLE vehicle_category MODIFY $colonne DOUBLE PRECISION NOT NULL");
        }
    }
}
