<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * D-079 A, D-083 : abonné validé qui fait la course. 1 colonne vide ajoutée, rien n'est supprimé.
 */
final class Version20261125015415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Réservation : abonné retenu (lien facultatif vers l\'entreprise abonnée, D-079, D-083)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation ADD abonne_retenu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955F22ABC54 FOREIGN KEY (abonne_retenu_id) REFERENCES entreprise (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_42C84955F22ABC54 ON reservation (abonne_retenu_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955F22ABC54');
        $this->addSql('DROP INDEX IDX_42C84955F22ABC54 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP abonne_retenu_id');
    }
}
