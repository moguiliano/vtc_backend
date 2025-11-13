<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250320173251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation ADD distance DOUBLE PRECISION NOT NULL, ADD duree INT NOT NULL, ADD prix DOUBLE PRECISION NOT NULL, ADD is_guest TINYINT(1) NOT NULL, ADD guest_info LONGTEXT DEFAULT NULL, DROP lieu_arret, DROP nom, DROP telephone, DROP email, CHANGE stop stop_option TINYINT(1) DEFAULT NULL, CHANGE numero_vol stop_lieu LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation ADD numero_vol LONGTEXT DEFAULT NULL, ADD lieu_arret VARCHAR(255) DEFAULT NULL, ADD nom VARCHAR(255) NOT NULL, ADD telephone VARCHAR(20) NOT NULL, ADD email VARCHAR(255) DEFAULT NULL, DROP stop_lieu, DROP distance, DROP duree, DROP prix, DROP is_guest, DROP guest_info, CHANGE stop_option stop TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES reservation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
