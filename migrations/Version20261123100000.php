<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fondation (D-020) — 1/7 : table `entreprise` + SIMPLICI CAB = entreprise n°1.
 */
final class Version20261123100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fondation 1/7 : table entreprise + SIMPLICI CAB (id 1)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql("INSERT INTO entreprise (id, nom, created_at) VALUES (1, 'SIMPLICI CAB', NOW())");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE entreprise');
    }
}
