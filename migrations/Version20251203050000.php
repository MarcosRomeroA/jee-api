<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add notification type for post moderation
 */
final class Version20251203050000 extends AbstractMigration
{
    private const NOTIFICATION_TYPE_ID = 'b50e8400-e29b-41d4-a716-446655440003';
    private const NOTIFICATION_TYPE_NAME = 'post_moderated';

    public function getDescription(): string
    {
        return 'Add notification type for post moderation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
            ['id' => self::NOTIFICATION_TYPE_ID, 'name' => self::NOTIFICATION_TYPE_NAME]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            "DELETE FROM notification_type WHERE id = :id",
            ['id' => self::NOTIFICATION_TYPE_ID]
        );
    }
}
