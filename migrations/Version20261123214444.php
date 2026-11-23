<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Régulateur (D-067, D-068) : chauffeur retenu + dernière modification sur la réservation.
 * 3 colonnes vides ajoutées, rien n'est supprimé.
 */
final class Version20261123214444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Réservation : chauffeur retenu, modifiée par, modifiée le (D-067, D-068)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation ADD chauffeur_retenu VARCHAR(255) DEFAULT NULL, ADD modifie_par VARCHAR(180) DEFAULT NULL, ADD modifie_le DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation DROP chauffeur_retenu, DROP modifie_par, DROP modifie_le');
    }
}
