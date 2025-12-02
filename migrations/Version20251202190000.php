<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Rename account_id to accountId in game_account_requirement for consistency with camelCase naming
 */
final class Version20251202190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename account_id to accountId in game requirements';
    }

    public function up(Schema $schema): void
    {
        // Valorant: riot_id format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"accountId\": true, \"format\": \"riot_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440080'");

        // League of Legends: riot_id format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"accountId\": true, \"format\": \"riot_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440081'");

        // Counter-Strike 2: steam_id format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"accountId\": true, \"format\": \"steam_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440082'");

        // Dota 2: steam_id format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"accountId\": true, \"format\": \"steam_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440083'");
    }

    public function down(Schema $schema): void
    {
        // Revert to account_id
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"account_id\": true, \"format\": \"riot_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440080'");
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"account_id\": true, \"format\": \"riot_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440081'");
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"account_id\": true, \"format\": \"steam_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440082'");
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"account_id\": true, \"format\": \"steam_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440083'");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
