<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260927231029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // DEFAULT CURRENT_TIMESTAMP backfille les lignes existantes (created_at devient NOT NULL) ; Doctrine fixe explicitement la valeur à chaque insert applicatif
        $this->addSql('ALTER TABLE `admin` ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE forfait ADD distance DOUBLE PRECISION DEFAULT NULL, ADD duree INT DEFAULT NULL, ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reservation ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', CHANGE type_vehicule type_vehicule VARCHAR(50) DEFAULT NULL, CHANGE mode_reglement mode_reglement VARCHAR(20) NOT NULL, CHANGE statut statut VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` DROP created_at');
        $this->addSql('ALTER TABLE reservation DROP created_at, CHANGE type_vehicule type_vehicule VARCHAR(50) NOT NULL, CHANGE mode_reglement mode_reglement VARCHAR(20) DEFAULT \'carte_bancaire\' NOT NULL, CHANGE statut statut VARCHAR(30) DEFAULT \'en_attente\' NOT NULL');
        $this->addSql('ALTER TABLE forfait DROP distance, DROP duree, DROP created_at');
    }
}
