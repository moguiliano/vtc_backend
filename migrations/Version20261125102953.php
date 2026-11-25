<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Étape 3 des espaces abonnés (D-087 à D-091) : 2 réglages de nuit sur entreprise, 1 colonne sur grille_tarif,
 * prix au seuil recalculé (inchangé pour SIMPLICI CAB), grilles copiées pour les abonnés déjà validés.
 * Rien n'est supprimé (sauf dans down()).
 */
final class Version20261125102953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Mes prix (abonnés) : majoration de nuit par abonné, suivi des prix ZenCAR, prix au seuil calculé, grilles des abonnés validés (D-087 à D-091)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE entreprise ADD nuit_active TINYINT(1) DEFAULT 1 NOT NULL, ADD nuit_pourcent DOUBLE PRECISION DEFAULT \'20\' NOT NULL');
        $this->addSql('ALTER TABLE grille_tarif ADD suit_zencar TINYINT(1) DEFAULT 0 NOT NULL');
        // D-090 : le prix au seuil est toujours prise en charge + seuil × €/km (déjà le cas pour SIMPLICI CAB)
        $this->addSql('UPDATE grille_tarif SET base_price_over_threshold = ROUND(base_price_under_threshold + threshold_km * price_per_km_under_threshold, 2)');
        // D-087, D-091 : grille des abonnés déjà validés, copiée de SIMPLICI CAB, rien de coché, « comme ZenCAR »
        $this->addSql("INSERT INTO grille_tarif (entreprise_id, categorie_id, actif, suit_zencar, threshold_km, base_price_under_threshold,
                price_per_km_under_threshold, base_price_over_threshold, price_per_km_over_threshold)
            SELECT e.id, g.categorie_id, 0, 1, g.threshold_km, g.base_price_under_threshold,
                g.price_per_km_under_threshold, g.base_price_over_threshold, g.price_per_km_over_threshold
            FROM entreprise e JOIN grille_tarif g ON g.entreprise_id = 1
            WHERE e.id <> 1 AND e.etat <> 'en_attente'
              AND NOT EXISTS (SELECT 1 FROM grille_tarif x WHERE x.entreprise_id = e.id AND x.categorie_id = g.categorie_id)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM grille_tarif WHERE entreprise_id <> 1');
        $this->addSql('ALTER TABLE entreprise DROP nuit_active, DROP nuit_pourcent');
        $this->addSql('ALTER TABLE grille_tarif DROP suit_zencar');
    }
}
