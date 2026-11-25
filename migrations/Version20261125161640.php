<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * D-108, D-109 : 2 colonnes vides sur compte_abonne. Rien n'est supprimé.
 */
final class Version20261125161640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Compte abonné : lien « mot de passe oublié » (empreinte + expiration, D-108)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE compte_abonne ADD reset_hash VARCHAR(64) DEFAULT NULL, ADD reset_expire DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE compte_abonne DROP reset_hash, DROP reset_expire');
    }
}
