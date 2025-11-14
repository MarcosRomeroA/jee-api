<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Player;

use App\Tests\Behat\Shared\Fixtures\TestUsers;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class PlayerTestContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    /** @BeforeScenario @player */
    public function createTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // Crear Game si no existe
        $gameExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game WHERE id = :id",
            ["id" => "550e8400-e29b-41d4-a716-446655440080"],
        );

        if ($gameExists == 0) {
            $connection->executeStatement(
                "INSERT INTO game (id, name, description, min_players_quantity, max_players_quantity, created_at)
                 VALUES (:id, :name, :description, :min, :max, NOW())",
                [
                    "id" => "550e8400-e29b-41d4-a716-446655440080",
                    "name" => "League of Legends",
                    "description" => "Test game",
                    "min" => 5,
                    "max" => 5,
                ],
            );
        }

        // Crear Role si no existe
        $roleExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM role WHERE id = :id",
            ["id" => "650e8400-e29b-41d4-a716-446655440001"],
        );

        if ($roleExists == 0) {
            $connection->executeStatement(
                "INSERT INTO role (id, name) VALUES (:id, :name)",
                [
                    "id" => "650e8400-e29b-41d4-a716-446655440001",
                    "name" => "Mid Laner",
                ],
            );
        }

        // Crear GameRole si no existe
        $gameRoleExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_role WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440001"],
        );

        if ($gameRoleExists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_role (id, role_id, game_id) VALUES (:id, :roleId, :gameId)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440001",
                    "roleId" => "650e8400-e29b-41d4-a716-446655440001",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                ],
            );
        }

        // Crear Rank si no existe
        $rankExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM rank WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440011"],
        );

        if ($rankExists == 0) {
            $connection->executeStatement(
                "INSERT INTO rank (id, name, description) VALUES (:id, :name, :description)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440011",
                    "name" => "Gold",
                    "description" => "Gold rank",
                ],
            );
        }

        // Crear GameRank si no existe
        $gameRankExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_rank WHERE id = :id",
            ["id" => "850e8400-e29b-41d4-a716-446655440011"],
        );

        if ($gameRankExists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_rank (id, rank_id, game_id, level) VALUES (:id, :rankId, :gameId, :level)",
                [
                    "id" => "850e8400-e29b-41d4-a716-446655440011",
                    "rankId" => "750e8400-e29b-41d4-a716-446655440011",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                    "level" => 5,
                ],
            );
        }

        // Crear Role adicional si no existe
        $role2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM role WHERE id = :id",
            ["id" => "650e8400-e29b-41d4-a716-446655440002"],
        );

        if ($role2Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO role (id, name) VALUES (:id, :name)",
                [
                    "id" => "650e8400-e29b-41d4-a716-446655440002",
                    "name" => "Top Laner",
                ],
            );
        }

        // Crear GameRole adicional si no existe
        $gameRole2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_role WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440002"],
        );

        if ($gameRole2Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_role (id, role_id, game_id) VALUES (:id, :roleId, :gameId)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440002",
                    "roleId" => "650e8400-e29b-41d4-a716-446655440002",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                ],
            );
        }

        // Crear Rank adicional si no existe
        $rank2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM rank WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440013"],
        );

        if ($rank2Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO rank (id, name, description) VALUES (:id, :name, :description)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440013",
                    "name" => "Platinum",
                    "description" => "Platinum rank",
                ],
            );
        }

        // Crear GameRank adicional si no existe
        $gameRank2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_rank WHERE id = :id",
            ["id" => "850e8400-e29b-41d4-a716-446655440013"],
        );

        if ($gameRank2Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_rank (id, rank_id, game_id, level) VALUES (:id, :rankId, :gameId, :level)",
                [
                    "id" => "850e8400-e29b-41d4-a716-446655440013",
                    "rankId" => "750e8400-e29b-41d4-a716-446655440013",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                    "level" => 7,
                ],
            );
        }

        // Crear Rank adicional si no existe (para update tests)
        $rank3Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM rank WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440010"],
        );

        if ($rank3Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO rank (id, name, description) VALUES (:id, :name, :description)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440010",
                    "name" => "Silver",
                    "description" => "Silver rank",
                ],
            );
        }

        // Crear GameRank adicional si no existe (para update tests)
        $gameRank3Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_rank WHERE id = :id",
            ["id" => "850e8400-e29b-41d4-a716-446655440010"],
        );

        if ($gameRank3Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_rank (id, rank_id, game_id, level) VALUES (:id, :rankId, :gameId, :level)",
                [
                    "id" => "850e8400-e29b-41d4-a716-446655440010",
                    "rankId" => "750e8400-e29b-41d4-a716-446655440010",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                    "level" => 3,
                ],
            );
        }

        // Crear Role adicional para update test (ADC)
        $role3Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM role WHERE id = :id",
            ["id" => "650e8400-e29b-41d4-a716-446655440005"],
        );

        if ($role3Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO role (id, name) VALUES (:id, :name)",
                [
                    "id" => "650e8400-e29b-41d4-a716-446655440005",
                    "name" => "ADC",
                ],
            );
        }

        // Crear GameRole adicional para update test
        $gameRole3Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_role WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440005"],
        );

        if ($gameRole3Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_role (id, role_id, game_id) VALUES (:id, :roleId, :gameId)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440005",
                    "roleId" => "650e8400-e29b-41d4-a716-446655440005",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                ],
            );
        }

        // Crear Role adicional para update test (Support)
        $role4Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM role WHERE id = :id",
            ["id" => "650e8400-e29b-41d4-a716-446655440007"],
        );

        if ($role4Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO role (id, name) VALUES (:id, :name)",
                [
                    "id" => "650e8400-e29b-41d4-a716-446655440007",
                    "name" => "Support",
                ],
            );
        }

        // Crear GameRole adicional para update test
        $gameRole4Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_role WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440007"],
        );

        if ($gameRole4Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_role (id, role_id, game_id) VALUES (:id, :roleId, :gameId)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440007",
                    "roleId" => "650e8400-e29b-41d4-a716-446655440007",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                ],
            );
        }

        // Crear Rank adicional para update test (Bronze)
        $rank4Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM rank WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440101"],
        );

        if ($rank4Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO rank (id, name, description) VALUES (:id, :name, :description)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440101",
                    "name" => "Bronze",
                    "description" => "Bronze rank",
                ],
            );
        }

        // Crear GameRank adicional para update test
        $gameRank4Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_rank WHERE id = :id",
            ["id" => "850e8400-e29b-41d4-a716-446655440101"],
        );

        if ($gameRank4Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_rank (id, rank_id, game_id, level) VALUES (:id, :rankId, :gameId, :level)",
                [
                    "id" => "850e8400-e29b-41d4-a716-446655440101",
                    "rankId" => "750e8400-e29b-41d4-a716-446655440101",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                    "level" => 1,
                ],
            );
        }

        // Crear Rank adicional para update test (Diamond)
        $rank5Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM rank WHERE id = :id",
            ["id" => "750e8400-e29b-41d4-a716-446655440120"],
        );

        if ($rank5Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO rank (id, name, description) VALUES (:id, :name, :description)",
                [
                    "id" => "750e8400-e29b-41d4-a716-446655440120",
                    "name" => "Diamond",
                    "description" => "Diamond rank",
                ],
            );
        }

        // Crear GameRank adicional para update test
        $gameRank5Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game_rank WHERE id = :id",
            ["id" => "850e8400-e29b-41d4-a716-446655440120"],
        );

        if ($gameRank5Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO game_rank (id, rank_id, game_id, level) VALUES (:id, :rankId, :gameId, :level)",
                [
                    "id" => "850e8400-e29b-41d4-a716-446655440120",
                    "rankId" => "750e8400-e29b-41d4-a716-446655440120",
                    "gameId" => "550e8400-e29b-41d4-a716-446655440080",
                    "level" => 9,
                ],
            );
        }
    }

    /** @AfterScenario @player */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Limpiar solo los players del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM player WHERE user_id = :userId",
                ["userId" => TestUsers::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // NO eliminar el usuario - es compartido entre contextos
        // Limpiar el entity manager
        $this->entityManager->clear();
    }
}
