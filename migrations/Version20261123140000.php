<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fondation (D-020) — 5/7 : grille de prix par entreprise (D-016).
 * Les prix actuels de vehicle_category sont COPIÉS dans la grille de SIMPLICI CAB (entreprise n°1).
 * vehicle_category n'est pas modifiée ici (ses anciennes colonnes de prix sont retirées en 7/7).
 */
final class Version20261123140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fondation 5/7 : table grille_tarif + copie des prix actuels pour l\'entreprise 1';
    }

    /** MariaDB/MySQL valident chaque ALTER TABLE tout de suite : pas de transaction possible. */
    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE grille_tarif (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, entreprise_id INT NOT NULL, actif TINYINT(1) NOT NULL, threshold_km DOUBLE PRECISION NOT NULL, base_price_under_threshold DOUBLE PRECISION NOT NULL, price_per_km_under_threshold DOUBLE PRECISION NOT NULL, base_price_over_threshold DOUBLE PRECISION NOT NULL, price_per_km_over_threshold DOUBLE PRECISION NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7DB5E790BCF5E72D (categorie_id), INDEX IDX_7DB5E790A4AEAFEA (entreprise_id), UNIQUE INDEX uniq_grille_entreprise_categorie (entreprise_id, categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE grille_tarif ADD CONSTRAINT FK_7DB5E790BCF5E72D FOREIGN KEY (categorie_id) REFERENCES vehicle_category (id)');
        $this->addSql('ALTER TABLE grille_tarif ADD CONSTRAINT FK_7DB5E790A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');

        // Une ligne par catégorie existante, avec exactement les mêmes prix
        $this->addSql('INSERT INTO grille_tarif (categorie_id, entreprise_id, actif, threshold_km, base_price_under_threshold, price_per_km_under_threshold, base_price_over_threshold, price_per_km_over_threshold, updated_at)
            SELECT id, 1, 1, threshold_km, base_price_under_threshold, price_per_km_under_threshold, base_price_over_threshold, price_per_km_over_threshold, updated_at
            FROM vehicle_category ORDER BY id');
    }

    public function down(Schema $schema): void
    {
        // Les prix ont pu être modifiés dans la grille : on les recopie dans vehicle_category avant de supprimer la table
        $this->addSql('UPDATE vehicle_category vc JOIN grille_tarif g ON g.categorie_id = vc.id AND g.entreprise_id = 1
            SET vc.threshold_km = g.threshold_km,
                vc.base_price_under_threshold = g.base_price_under_threshold,
                vc.price_per_km_under_threshold = g.price_per_km_under_threshold,
                vc.base_price_over_threshold = g.base_price_over_threshold,
                vc.price_per_km_over_threshold = g.price_per_km_over_threshold');
        $this->addSql('DROP TABLE grille_tarif');
    }
}
