<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to insert initial games into the platform
 */
final class Version20251112150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert initial games: Valorant, League of Legends, Counter Strike, Dota 2';
    }

    public function up(Schema $schema): void
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        // Valorant
        $this->addSql(
            'INSERT INTO game (id, name, description, min_players_quantity, max_players_quantity, created_at) 
             VALUES (:id, :name, :description, :min_players, :max_players, :created_at)',
            [
                'id' => '550e8400-e29b-41d4-a716-446655440080',
                'name' => 'Valorant',
                'description' => 'Tactical first-person shooter developed by Riot Games',
                'min_players' => 5,
                'max_players' => 5,
                'created_at' => $now
            ]
        );

        // League of Legends
        $this->addSql(
            'INSERT INTO game (id, name, description, min_players_quantity, max_players_quantity, created_at) 
             VALUES (:id, :name, :description, :min_players, :max_players, :created_at)',
            [
                'id' => '550e8400-e29b-41d4-a716-446655440081',
                'name' => 'League of Legends',
                'description' => 'Multiplayer online battle arena game developed by Riot Games',
                'min_players' => 5,
                'max_players' => 5,
                'created_at' => $now
            ]
        );

        // Counter Strike 2
        $this->addSql(
            'INSERT INTO game (id, name, description, min_players_quantity, max_players_quantity, created_at) 
             VALUES (:id, :name, :description, :min_players, :max_players, :created_at)',
            [
                'id' => '550e8400-e29b-41d4-a716-446655440082',
                'name' => 'Counter-Strike 2',
                'description' => 'Tactical first-person shooter developed by Valve',
                'min_players' => 5,
                'max_players' => 5,
                'created_at' => $now
            ]
        );

        // Dota 2
        $this->addSql(
            'INSERT INTO game (id, name, description, min_players_quantity, max_players_quantity, created_at) 
             VALUES (:id, :name, :description, :min_players, :max_players, :created_at)',
            [
                'id' => '550e8400-e29b-41d4-a716-446655440083',
                'name' => 'Dota 2',
                'description' => 'Multiplayer online battle arena game developed by Valve',
                'min_players' => 5,
                'max_players' => 5,
                'created_at' => $now
            ]
        );
    }

    public function down(Schema $schema): void
    {
        // Eliminar los juegos insertados
        $this->addSql("DELETE FROM game WHERE id IN (
            '550e8400-e29b-41d4-a716-446655440080',
            '550e8400-e29b-41d4-a716-446655440081',
            '550e8400-e29b-41d4-a716-446655440082',
            '550e8400-e29b-41d4-a716-446655440083'
        )");
    }

    public function isTransactional(): bool
    {
        return false;
    }
}

