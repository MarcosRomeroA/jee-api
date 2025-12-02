<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Update game_account_requirement to use format-based requirements (riot_id, steam_id)
 * instead of individual fields (region, username, tag, steam_profile)
 */
final class Version20251202174003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update game requirements to use riot_id and steam_id formats instead of individual fields';
    }

    public function up(Schema $schema): void
    {
        // All games now use account_id as the required field
        // The format field indicates how frontend should validate (riot_id = username#tag, steam_id = steam profile id)

        // Valorant: riot_id format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"account_id\": true, \"format\": \"riot_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440080'");

        // League of Legends: riot_id format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"account_id\": true, \"format\": \"riot_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440081'");

        // Counter-Strike 2: steam_id format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"account_id\": true, \"format\": \"steam_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440082'");

        // Dota 2: steam_id format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"account_id\": true, \"format\": \"steam_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440083'");
    }

    public function down(Schema $schema): void
    {
        // Revert Valorant
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"region\": true, \"username\": true, \"tag\": true}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440080'");

        // Revert League of Legends
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"region\": true, \"username\": true, \"tag\": true}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440081'");

        // Revert Counter-Strike 2
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"steam_profile\": true}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440082'");

        // Revert Dota 2
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"steam_profile\": true}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440083'");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
