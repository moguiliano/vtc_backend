<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * D-009 : réglages du site (note et nombre d'avis Google), modifiables dans EasyAdmin.
 * Crée la table et sa ligne unique avec les valeurs relevées sur la fiche Google le 2026-11-15.
 */
final class Version20261115120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table site_settings (note et nombre d\'avis Google) + ligne initiale 5,0 / 124';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE site_settings (id INT AUTO_INCREMENT NOT NULL, google_rating NUMERIC(2, 1) NOT NULL, google_review_count INT NOT NULL, google_reviews_url VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO site_settings (google_rating, google_review_count, google_reviews_url, updated_at) VALUES (5.0, 124, \'https://share.google/5lmVFiBFJ5YK0O7ds\', NOW())');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE site_settings');
    }
}
