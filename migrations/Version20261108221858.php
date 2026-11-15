<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261108221858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, numero VARCHAR(30) NOT NULL, date_facture DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', date_transaction DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', client_nom VARCHAR(150) NOT NULL, client_adresse LONGTEXT DEFAULT NULL, client_siret VARCHAR(20) DEFAULT NULL, client_tva VARCHAR(30) DEFAULT NULL, description VARCHAR(255) NOT NULL, prix_ttc DOUBLE PRECISION NOT NULL, taux_tva DOUBLE PRECISION NOT NULL, mode_reglement VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_FE866410F55AE19E (numero), INDEX IDX_FE866410B83297E7 (reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410B83297E7');
        $this->addSql('DROP TABLE facture');
    }
}
