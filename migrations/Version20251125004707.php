<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251125004707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add role column to admin table and set default admin as superadmin';
    }

    public function up(Schema $schema): void
    {
        // Add role column with default value 'admin'
        $this->addSql("ALTER TABLE admin ADD role VARCHAR(20) NOT NULL DEFAULT 'admin'");

        // Update default admin to superadmin role
        $this->addSql("UPDATE admin SET role = 'superadmin' WHERE id = 'a50e8400-e29b-41d4-a716-446655440000'");
    }

    public function down(Schema $schema): void
    {
        // Remove role column
        $this->addSql('ALTER TABLE admin DROP role');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
