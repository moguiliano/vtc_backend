<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Espaces abonnés, étape 1 (D-073 à D-078).
 * 3 colonnes ajoutées sur entreprise (SIMPLICI CAB mise à « actif ») + nouvelle table compte_abonne.
 * Rien n'est supprimé.
 */
final class Version20261125003725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Espaces abonnés étape 1 : type, état, début d\'essai sur entreprise + table compte_abonne (D-074 à D-078)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE compte_abonne (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_ACABD2C7A4AEAFEA (entreprise_id), UNIQUE INDEX uniq_compte_abonne_email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compte_abonne ADD CONSTRAINT FK_ACABD2C7A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE entreprise ADD type_abonnement VARCHAR(20) DEFAULT NULL, ADD etat VARCHAR(20) DEFAULT NULL, ADD essai_debut DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
        // SIMPLICI CAB (et toute entreprise déjà là) : active, pas un abonné
        $this->addSql("UPDATE entreprise SET etat = 'actif'");
        $this->addSql('ALTER TABLE entreprise MODIFY etat VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE compte_abonne DROP FOREIGN KEY FK_ACABD2C7A4AEAFEA');
        $this->addSql('DROP TABLE compte_abonne');
        $this->addSql('ALTER TABLE entreprise DROP type_abonnement, DROP etat, DROP essai_debut');
    }
}
