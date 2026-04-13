<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table vehicle_category (tarification véhicules depuis la BDD)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE vehicle_category (
            id INT AUTO_INCREMENT NOT NULL,
            slug VARCHAR(50) NOT NULL,
            label VARCHAR(100) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            base_price_under_threshold DOUBLE PRECISION NOT NULL,
            base_price_over_threshold DOUBLE PRECISION NOT NULL,
            price_per_km_under_threshold DOUBLE PRECISION NOT NULL,
            price_per_km_over_threshold DOUBLE PRECISION NOT NULL,
            threshold_km DOUBLE PRECISION NOT NULL,
            max_passengers INT NOT NULL,
            luggage_capacity INT NOT NULL,
            image_filename VARCHAR(255) DEFAULT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            display_order INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE INDEX UNIQ_SLUG (slug),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE vehicle_category');
    }
}
