<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261108222750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE societe (id INT AUTO_INCREMENT NOT NULL, raison_sociale VARCHAR(150) NOT NULL, nom_commercial VARCHAR(150) NOT NULL, forme_juridique VARCHAR(50) NOT NULL, capital_social VARCHAR(50) DEFAULT NULL, rcs VARCHAR(100) DEFAULT NULL, adresse VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, ville VARCHAR(100) NOT NULL, telephone VARCHAR(30) NOT NULL, email VARCHAR(150) DEFAULT NULL, siren VARCHAR(20) NOT NULL, siret VARCHAR(25) NOT NULL, tva_intra VARCHAR(30) NOT NULL, code_naf VARCHAR(10) NOT NULL, numero_revtc VARCHAR(30) NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql("INSERT INTO societe (raison_sociale, nom_commercial, forme_juridique, adresse, code_postal, ville, telephone, siren, siret, tva_intra, code_naf, numero_revtc) VALUES ('SIMPLICI CAB', 'ZenCAR', 'SAS', '12 Rue Antoine Re', '13010', 'Marseille', '06 74 03 96 94', '982 953 382', '982 953 382 00024', 'FR18 982 953 382', '49.32Z', 'EVTC094240137')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE societe');
    }
}
