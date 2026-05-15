<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Feature: informations_complementaires, mode_reglement, statut (ReservationStatus)
 */
final class Version20260516120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add informations_complementaires, mode_reglement and statut to reservation table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE reservation
            ADD COLUMN informations_complementaires LONGTEXT DEFAULT NULL,
            ADD COLUMN mode_reglement VARCHAR(20) NOT NULL DEFAULT 'carte_bancaire',
            ADD COLUMN statut VARCHAR(30) NOT NULL DEFAULT 'en_attente'
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation
            DROP COLUMN informations_complementaires,
            DROP COLUMN mode_reglement,
            DROP COLUMN statut
        ');
    }
}
