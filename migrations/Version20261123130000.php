<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fondation (D-020) — 4/7 : chaque facture appartient à l'entreprise qui l'émet (D-017).
 * Factures existantes → entreprise n°1. Le numéro FAC-AAAA-0001 devient unique PAR entreprise
 * (au lieu d'unique dans toute la table). Aucun numéro existant ne change.
 */
final class Version20261123130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fondation 4/7 : facture.entreprise_id (existantes → entreprise 1) + numéro unique par entreprise';
    }

    /** MariaDB/MySQL valident chaque ALTER TABLE tout de suite : pas de transaction possible. */
    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE facture ADD entreprise_id INT DEFAULT NULL');
        $this->addSql('UPDATE facture SET entreprise_id = 1');
        $this->addSql('ALTER TABLE facture MODIFY entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_FE866410A4AEAFEA ON facture (entreprise_id)');
        // Nouvelle règle d'unicité d'abord, ancienne ensuite : le numéro n'est jamais sans protection
        $this->addSql('CREATE UNIQUE INDEX uniq_facture_entreprise_numero ON facture (entreprise_id, numero)');
        $this->addSql('DROP INDEX UNIQ_FE866410F55AE19E ON facture');
    }

    public function down(Schema $schema): void
    {
        // Échoue volontairement si deux entreprises ont le même numéro (retour impossible sans perte)
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE866410F55AE19E ON facture (numero)');
        $this->addSql('DROP INDEX uniq_facture_entreprise_numero ON facture');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410A4AEAFEA');
        $this->addSql('DROP INDEX IDX_FE866410A4AEAFEA ON facture');
        $this->addSql('ALTER TABLE facture DROP entreprise_id');
    }
}
