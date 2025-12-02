<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to remove the username column from player table.
 * The username is now stored within the accountData JSON field.
 */
final class Version20251202160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove username column from player table (username is now in account_data)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE player DROP COLUMN username');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE player ADD username VARCHAR(50) NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
