<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fondation (D-020) — 2/7 : chaque réservation appartient à une entreprise.
 * Les réservations existantes sont rattachées à SIMPLICI CAB (entreprise n°1). Aucune ligne supprimée.
 */
final class Version20261123110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fondation 2/7 : reservation.entreprise_id (existantes → entreprise 1)';
    }

    /** MariaDB/MySQL valident chaque ALTER TABLE tout de suite : pas de transaction possible. */
    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        // 1) colonne vide, 2) remplie avec 1, 3) rendue obligatoire, 4) clé étrangère + index
        $this->addSql('ALTER TABLE reservation ADD entreprise_id INT DEFAULT NULL');
        $this->addSql('UPDATE reservation SET entreprise_id = 1');
        $this->addSql('ALTER TABLE reservation MODIFY entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_42C84955A4AEAFEA ON reservation (entreprise_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A4AEAFEA');
        $this->addSql('DROP INDEX IDX_42C84955A4AEAFEA ON reservation');
        $this->addSql('ALTER TABLE reservation DROP entreprise_id');
    }
}
