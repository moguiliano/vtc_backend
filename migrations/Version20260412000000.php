<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Correctifs critiques : VerificationCode (expires_at, attempts, last_sent_at) + User (prenom)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE verification_code
            ADD expires_at DATETIME DEFAULT NULL,
            ADD attempts INT NOT NULL DEFAULT 0,
            ADD last_sent_at DATETIME DEFAULT NULL
        ');

        $this->addSql('ALTER TABLE user ADD prenom VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE verification_code DROP expires_at, DROP attempts, DROP last_sent_at');
        $this->addSql('ALTER TABLE user DROP prenom');
    }
}
