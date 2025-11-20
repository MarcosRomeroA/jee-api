<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add 3 test users (tester1, tester2, tester3) with password 12345678
 */
final class Version20251119000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add 3 verified test users with password 12345678';
    }

    public function up(Schema $schema): void
    {
        $now = date('Y-m-d H:i:s');
        $passwordHash = '$2y$12$t4UxVRA8BWWJ/dgTx5HeQeYUAdyiW9hk4PFiZ4iz9.M8IkLjCoqq6'; // 12345678

        // User 1: tester1 - ID FIJO para tests
        $this->addSql("
            INSERT INTO `user` (id, firstname, lastname, username, email, password, profile_image, description, created_at, verified_at)
            VALUES (
                '550e8400-e29b-41d4-a716-446655440001',
                'Tester',
                'One',
                'tester1',
                'tester1@test.com',
                '{$passwordHash}',
                '',
                'Test user 1',
                '{$now}',
                '{$now}'
            )
        ");

        // User 2: tester2 - ID FIJO para tests
        $this->addSql("
            INSERT INTO `user` (id, firstname, lastname, username, email, password, profile_image, description, created_at, verified_at)
            VALUES (
                '550e8400-e29b-41d4-a716-446655440002',
                'Tester',
                'Two',
                'tester2',
                'tester2@test.com',
                '{$passwordHash}',
                '',
                'Test user 2',
                '{$now}',
                '{$now}'
            )
        ");

        // User 3: tester3 - ID FIJO para tests
        $this->addSql("
            INSERT INTO `user` (id, firstname, lastname, username, email, password, profile_image, description, created_at, verified_at)
            VALUES (
                '550e8400-e29b-41d4-a716-446655440003',
                'Tester',
                'Three',
                'tester3',
                'tester3@test.com',
                '{$passwordHash}',
                '',
                'Test user 3',
                '{$now}',
                '{$now}'
            )
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `user` WHERE username IN ('tester1', 'tester2', 'tester3')");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
