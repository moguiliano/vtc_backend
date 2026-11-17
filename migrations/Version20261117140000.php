<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Journal des appels entrants (source, statut, gain) pour mesurer la rentabilité Google Ads.
 */
final class Version20261117140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table appel : journal des appels (source, statut, motif de refus, gain)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE appel (id INT AUTO_INCREMENT NOT NULL, date_appel DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_course DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', source VARCHAR(30) NOT NULL, type_service VARCHAR(20) DEFAULT NULL, statut VARCHAR(20) NOT NULL, motif_refus VARCHAR(30) DEFAULT NULL, trajet VARCHAR(150) DEFAULT NULL, prix_course NUMERIC(8, 2) DEFAULT NULL, gain NUMERIC(8, 2) DEFAULT NULL, observation LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX idx_appel_date (date_appel), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE appel');
    }
}
