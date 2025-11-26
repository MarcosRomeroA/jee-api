<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124232055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create default admin user';
    }

    public function up(Schema $schema): void
    {
        // Create default admin user with a pre-hashed password (BCrypt hash of 'admin')
        // Hash generated with: password_hash('admin', PASSWORD_BCRYPT)
        // Using single quotes to avoid PHP variable interpolation with $ symbols
        // Username is 'admin' (not 'admin@admin.com') because AdminUserValue only allows letters, numbers, dots, hyphens and underscores
        // Using a valid UUID v4 (not all zeros)
        $this->addSql(
            'INSERT INTO admin (id, name, user, password, created_at)
             VALUES (\'a50e8400-e29b-41d4-a716-446655440000\', \'Admin\', \'admin\', \'$2y$10$hxjNLjl1ipFCbo6.qpX3OOswHDoRmop3DhhBvF97VvewHDB0xcyeK\', NOW())'
        );
    }

    public function down(Schema $schema): void
    {
        // Remove default admin user
        $this->addSql("DELETE FROM admin WHERE id = 'a50e8400-e29b-41d4-a716-446655440000'");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
