<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add notification types for team and tournament request notifications
 */
final class Version20251123210000 extends AbstractMigration
{
    private const NOTIFICATION_TYPES = [
        'team_request_received' => 'b50e8400-e29b-41d4-a716-446655440001',
        'tournament_request_received' => 'b50e8400-e29b-41d4-a716-446655440002',
    ];

    public function getDescription(): string
    {
        return 'Add notification types for team and tournament request notifications';
    }

    public function up(Schema $schema): void
    {
        foreach (self::NOTIFICATION_TYPES as $name => $uuid) {
            $this->addSql(
                "INSERT INTO notification_type (id, name) VALUES ('$uuid', '$name')"
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::NOTIFICATION_TYPES as $name => $uuid) {
            $this->addSql("DELETE FROM notification_type WHERE id = '$uuid'");
        }
    }
}
