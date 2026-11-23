<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fondation (D-020) — 3/7 : une fiche société par entreprise (D-017).
 * La fiche existante (SIMPLICI CAB) est rattachée à l'entreprise n°1.
 */
final class Version20261123120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fondation 3/7 : societe.entreprise_id unique (fiche existante → entreprise 1)';
    }

    /** MariaDB/MySQL valident chaque ALTER TABLE tout de suite : pas de transaction possible. */
    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $nb = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM societe');
        $this->abortIf($nb > 1, "La table societe contient $nb fiches : une seule est attendue. Rien n'a été modifié.");

        $this->addSql('ALTER TABLE societe ADD entreprise_id INT DEFAULT NULL');
        $this->addSql('UPDATE societe SET entreprise_id = 1');
        $this->addSql('ALTER TABLE societe MODIFY entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE societe ADD CONSTRAINT FK_19653DBDA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_19653DBDA4AEAFEA ON societe (entreprise_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE societe DROP FOREIGN KEY FK_19653DBDA4AEAFEA');
        $this->addSql('DROP INDEX UNIQ_19653DBDA4AEAFEA ON societe');
        $this->addSql('ALTER TABLE societe DROP entreprise_id');
    }
}
