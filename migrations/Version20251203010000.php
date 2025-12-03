<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add Unranked game_rank entries for all games
 */
final class Version20251203010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Unranked game_rank entries for all games (Valorant, LoL, CS2, Dota2)';
    }

    public function up(Schema $schema): void
    {
        // Unranked rank ID
        $unrankedRankId = '00000000-0000-0000-0000-000000000001';

        // Game IDs
        $valorantId = '550e8400-e29b-41d4-a716-446655440080';
        $lolId = '550e8400-e29b-41d4-a716-446655440081';
        $cs2Id = '550e8400-e29b-41d4-a716-446655440082';
        $dota2Id = '550e8400-e29b-41d4-a716-446655440083';

        // Insert Unranked game_rank for each game with level 0
        $this->addSql("INSERT INTO game_rank (id, game_id, rank_id, level) VALUES
            ('00000000-0000-0000-0000-000000000080', '$valorantId', '$unrankedRankId', 0),
            ('00000000-0000-0000-0000-000000000081', '$lolId', '$unrankedRankId', 0),
            ('00000000-0000-0000-0000-000000000082', '$cs2Id', '$unrankedRankId', 0),
            ('00000000-0000-0000-0000-000000000083', '$dota2Id', '$unrankedRankId', 0)
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM game_rank WHERE rank_id = '00000000-0000-0000-0000-000000000001'");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
