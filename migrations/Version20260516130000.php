<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add guest_prenom and guest_telephone columns to reservation.
 * Also correct nullable for typeVehicule.
 */
final class Version20260516130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add guest_prenom and guest_telephone to reservation table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE reservation
            ADD COLUMN guest_prenom VARCHAR(100) DEFAULT NULL,
            ADD COLUMN guest_telephone VARCHAR(25) DEFAULT NULL
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation
            DROP COLUMN guest_prenom,
            DROP COLUMN guest_telephone
        ');
    }
}
