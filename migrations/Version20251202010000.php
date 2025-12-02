<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add demo users for web and backoffice
 */
final class Version20251202010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add demo users (john.doe, jane.doe, test.user) and admins (admin, superadmin) with password 12345678';
    }

    public function up(Schema $schema): void
    {
        $now = date('Y-m-d H:i:s');
        $passwordHash = '$2y$12$wwG90bTlMDXYFs92gDFSmOstETSBtW7H7Uebdi4gPkTHZwuGdk4Hi'; // Test12345678

        // Web users (verified)
        $this->addSql("
            INSERT INTO `user` (id, firstname, lastname, username, email, password, profile_image, background_image, description, created_at, verified_at)
            VALUES (
                '550e8400-e29b-41d4-a716-446655440010',
                'John',
                'Doe',
                'john.doe',
                'john.doe@test.com',
                '{$passwordHash}',
                '',
                '',
                'Classic male placeholder user',
                '{$now}',
                '{$now}'
            )
        ");

        $this->addSql("
            INSERT INTO `user` (id, firstname, lastname, username, email, password, profile_image, background_image, description, created_at, verified_at)
            VALUES (
                '550e8400-e29b-41d4-a716-446655440011',
                'Jane',
                'Doe',
                'jane.doe',
                'jane.doe@test.com',
                '{$passwordHash}',
                '',
                '',
                'Classic female placeholder user',
                '{$now}',
                '{$now}'
            )
        ");

        $this->addSql("
            INSERT INTO `user` (id, firstname, lastname, username, email, password, profile_image, background_image, description, created_at, verified_at)
            VALUES (
                '550e8400-e29b-41d4-a716-446655440012',
                'Test',
                'User',
                'test.user',
                'test.user@test.com',
                '{$passwordHash}',
                '',
                '',
                'Generic test user',
                '{$now}',
                '{$now}'
            )
        ");

        // Backoffice admins
        $this->addSql("
            INSERT INTO `admin` (id, name, user, password, role, created_at)
            VALUES (
                'a50e8400-e29b-41d4-a716-446655440001',
                'Admin User',
                'testadmin',
                '{$passwordHash}',
                'admin',
                '{$now}'
            )
        ");

        $this->addSql("
            INSERT INTO `admin` (id, name, user, password, role, created_at)
            VALUES (
                'a50e8400-e29b-41d4-a716-446655440002',
                'Super Admin',
                'testsuperadmin',
                '{$passwordHash}',
                'superadmin',
                '{$now}'
            )
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `user` WHERE email IN ('john.doe@test.com', 'jane.doe@test.com', 'test.user@test.com')");
        $this->addSql("DELETE FROM `admin` WHERE user IN ('testadmin', 'testsuperadmin')");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
