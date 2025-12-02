<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Update game_account_requirement to use separate fields per game type:
 * - Riot games (Valorant, LoL): region, username, tag
 * - Steam games (CS2, Dota 2): steamId
 */
final class Version20251202200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update game requirements to use separate fields (region, username, tag for Riot; steamId for Steam)';
    }

    public function up(Schema $schema): void
    {
        // Valorant: Riot game - needs region, username, tag
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"region\": true, \"username\": true, \"tag\": true}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440080'");

        // League of Legends: Riot game - needs region, username, tag
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"region\": true, \"username\": true, \"tag\": true}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440081'");

        // Counter-Strike 2: Steam game - needs steamId
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"steamId\": true}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440082'");

        // Dota 2: Steam game - needs steamId
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"steamId\": true}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440083'");
    }

    public function down(Schema $schema): void
    {
        // Revert to accountId format
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"accountId\": true, \"format\": \"riot_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440080'");
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"accountId\": true, \"format\": \"riot_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440081'");
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"accountId\": true, \"format\": \"steam_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440082'");
        $this->addSql("UPDATE game_account_requirement SET requirements = '{\"accountId\": true, \"format\": \"steam_id\"}' WHERE game_id = '550e8400-e29b-41d4-a716-446655440083'");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
