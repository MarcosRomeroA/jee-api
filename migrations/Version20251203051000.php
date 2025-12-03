<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add post_mention table and user_mentioned notification type
 */
final class Version20251203051000 extends AbstractMigration
{
    private const NOTIFICATION_TYPE_ID = 'b50e8400-e29b-41d4-a716-446655440004';
    private const NOTIFICATION_TYPE_NAME = 'user_mentioned';

    public function getDescription(): string
    {
        return 'Add post_mention table and user_mentioned notification type';
    }

    public function up(Schema $schema): void
    {
        // Create post_mention table
        $this->addSql('
            CREATE TABLE post_mention (
                id VARCHAR(36) NOT NULL,
                post_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                PRIMARY KEY(id),
                INDEX IDX_post_mention_post_id (post_id),
                INDEX IDX_post_mention_user_id (user_id),
                UNIQUE INDEX unique_post_mention (post_id, user_id),
                CONSTRAINT FK_post_mention_post FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
                CONSTRAINT FK_post_mention_user FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        // Add notification type
        $this->addSql(
            "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
            ['id' => self::NOTIFICATION_TYPE_ID, 'name' => self::NOTIFICATION_TYPE_NAME]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE post_mention');
        $this->addSql(
            "DELETE FROM notification_type WHERE id = :id",
            ['id' => self::NOTIFICATION_TYPE_ID]
        );
    }
}
