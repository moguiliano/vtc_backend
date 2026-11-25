<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Étape 2b (D-102, D-106, D-107) : 2 colonnes vides sur reservation. Rien n'est supprimé.
 */
final class Version20261125150533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Réservation : statut côté chauffeur et date (courses confiées aux abonnés, D-102, D-106, D-107)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation ADD statut_chauffeur VARCHAR(20) DEFAULT NULL, ADD statut_chauffeur_le DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation DROP statut_chauffeur, DROP statut_chauffeur_le');
    }
}
