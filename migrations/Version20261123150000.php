<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fondation (D-020) — 6/7 : forfaits, catégories de messages (et donc leurs modèles) et journal
 * des appels appartiennent à une entreprise. Lignes existantes → SIMPLICI CAB (entreprise n°1).
 */
final class Version20261123150000 extends AbstractMigration
{
    /** table => [nom de la clé étrangère, nom de l'index] (noms générés par Doctrine) */
    private const TABLES = [
        'forfait'          => ['FK_BBB5C482A4AEAFEA', 'IDX_BBB5C482A4AEAFEA'],
        'message_category' => ['FK_F6ADDD0A4AEAFEA', 'IDX_F6ADDD0A4AEAFEA'],
        'appel'            => ['FK_130D3BDA4AEAFEA', 'IDX_130D3BDA4AEAFEA'],
    ];

    public function getDescription(): string
    {
        return 'Fondation 6/7 : entreprise_id sur forfait, message_category, appel (existants → entreprise 1)';
    }

    /** MariaDB/MySQL valident chaque ALTER TABLE tout de suite : pas de transaction possible. */
    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        foreach (self::TABLES as $table => [$fk, $idx]) {
            $this->addSql("ALTER TABLE $table ADD entreprise_id INT DEFAULT NULL");
            $this->addSql("UPDATE $table SET entreprise_id = 1");
            $this->addSql("ALTER TABLE $table MODIFY entreprise_id INT NOT NULL");
            $this->addSql("ALTER TABLE $table ADD CONSTRAINT $fk FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)");
            $this->addSql("CREATE INDEX $idx ON $table (entreprise_id)");
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::TABLES as $table => [$fk, $idx]) {
            $this->addSql("ALTER TABLE $table DROP FOREIGN KEY $fk");
            $this->addSql("DROP INDEX $idx ON $table");
            $this->addSql("ALTER TABLE $table DROP entreprise_id");
        }
    }
}
