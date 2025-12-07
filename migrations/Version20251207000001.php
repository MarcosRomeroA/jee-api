<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251207000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add team_request_accepted and tournament_request_accepted notification types';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO notification_type (id, name) VALUES (UUID(), 'team_request_accepted')");
        $this->addSql("INSERT INTO notification_type (id, name) VALUES (UUID(), 'tournament_request_accepted')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM notification_type WHERE name = 'team_request_accepted'");
        $this->addSql("DELETE FROM notification_type WHERE name = 'tournament_request_accepted'");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
