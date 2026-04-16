<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416103133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forfait (id INT AUTO_INCREMENT NOT NULL, depart VARCHAR(100) NOT NULL, arrivee VARCHAR(100) NOT NULL, prix INT NOT NULL, actif TINYINT(1) NOT NULL, ordre INT NOT NULL, icone VARCHAR(60) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vehicle_category CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE display_order display_order INT NOT NULL');
        $this->addSql('ALTER TABLE vehicle_category RENAME INDEX uniq_slug TO UNIQ_DB5E1655989D9B62');
        $this->addSql('ALTER TABLE verification_code CHANGE phone_number phone_number VARCHAR(32) NOT NULL, CHANGE code code VARCHAR(6) NOT NULL');
        $this->addSql('CREATE INDEX idx_verifcode_phone ON verification_code (phone_number)');
        $this->addSql('CREATE INDEX idx_verifcode_check ON verification_code (phone_number, code, is_verified)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE forfait');
        $this->addSql('DROP INDEX idx_verifcode_phone ON verification_code');
        $this->addSql('DROP INDEX idx_verifcode_check ON verification_code');
        $this->addSql('ALTER TABLE verification_code CHANGE phone_number phone_number VARCHAR(255) NOT NULL, CHANGE code code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE vehicle_category CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL, CHANGE display_order display_order INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE vehicle_category RENAME INDEX uniq_db5e1655989d9b62 TO UNIQ_SLUG');
    }
}
