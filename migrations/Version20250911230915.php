<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911230915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "";
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
            [
                "id" => "550e8400-e29b-41d4-a716-446655440099",
                "name" => "new_message",
            ],
        );

        $this->addSql(
            "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
            [
                "id" => "850e8400-e29b-41d4-a716-446655440001",
                "name" => "post_liked",
            ],
        );

        $this->addSql(
            "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
            [
                "id" => "850e8400-e29b-41d4-a716-446655440002",
                "name" => "post_commented",
            ],
        );

        $this->addSql(
            "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
            [
                "id" => "750e8400-e29b-41d4-a716-446655440002",
                "name" => "post_shared",
            ],
        );

        $this->addSql(
            "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
            [
                "id" => "750e8400-e29b-41d4-a716-446655440001",
                "name" => "new_follower",
            ],
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM notification_type WHERE name IS NOT NULL");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
