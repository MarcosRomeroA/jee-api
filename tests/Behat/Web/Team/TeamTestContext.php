<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\Team;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\ValueObject\TeamNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamImageValue;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use App\Tests\Behat\Shared\Fixtures\TestUsers;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class TeamTestContext implements Context
{
    private const TEST_GAME_ID = "550e8400-e29b-41d4-a716-446655440071";
    private const TEST_GAME_ID_2 = "550e8400-e29b-41d4-a716-446655440002";
    private const TEST_TEAM_ID = "550e8400-e29b-41d4-a716-446655440072";
    private const TEST_TEAM_ID_2 = "550e8400-e29b-41d4-a716-446655440060";

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /** @BeforeScenario @team */
    public function createTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // IMPORTANTE: Limpiar team_requests ANTES de cada escenario para evitar contaminación
        try {
            $connection->executeStatement("DELETE FROM team_request");
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // Obtener o crear el usuario compartido USER1
        try {
            $user = $this->userRepository->findById(
                new Uuid(TestUsers::USER1_ID),
            );
        } catch (\Exception $e) {
            $user = User::create(
                new Uuid(TestUsers::USER1_ID),
                new FirstnameValue(TestUsers::USER1_FIRSTNAME),
                new LastnameValue(TestUsers::USER1_LASTNAME),
                new UsernameValue(TestUsers::USER1_USERNAME),
                new EmailValue(TestUsers::USER1_EMAIL),
                new PasswordValue(TestUsers::USER1_PASSWORD),
            );
            $this->userRepository->save($user);
        }

        // Crear USER2 si no existe
        $user2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM user WHERE id = :id",
            ["id" => TestUsers::USER2_ID],
        );

        if ($user2Exists == 0) {
            $user2 = User::create(
                new Uuid(TestUsers::USER2_ID),
                new FirstnameValue(TestUsers::USER2_FIRSTNAME),
                new LastnameValue(TestUsers::USER2_LASTNAME),
                new UsernameValue(TestUsers::USER2_USERNAME),
                new EmailValue(TestUsers::USER2_EMAIL),
                new PasswordValue(TestUsers::USER2_PASSWORD),
            );
            $this->userRepository->save($user2);
        }

        // Crear USER3 si no existe
        $user3Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM user WHERE id = :id",
            ["id" => TestUsers::USER3_ID],
        );

        if ($user3Exists == 0) {
            $user3 = User::create(
                new Uuid(TestUsers::USER3_ID),
                new FirstnameValue(TestUsers::USER3_FIRSTNAME),
                new LastnameValue(TestUsers::USER3_LASTNAME),
                new UsernameValue(TestUsers::USER3_USERNAME),
                new EmailValue(TestUsers::USER3_EMAIL),
                new PasswordValue(TestUsers::USER3_PASSWORD),
            );
            $this->userRepository->save($user3);
        }

        // Crear dependencias necesarias para Player (Game, Role, Rank, GameRole, GameRank)
        $this->createPlayerDependencies($connection);

        // Crear Player para USER2 si no existe
        $player2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM player WHERE id = :id",
            ["id" => TestUsers::USER2_ID],
        );

        if ($player2Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO player (id, user_id, game_rank_id, username, verified, created_at)
                 VALUES (:id, :userId, :gameRankId, :username, :verified, NOW())",
                [
                    "id" => TestUsers::USER2_ID,
                    "userId" => TestUsers::USER2_ID,
                    "gameRankId" => "850e8400-e29b-41d4-a716-446655440011",
                    "username" => "jane",
                    "verified" => 0,
                ],
            );

            // Crear relación player_game_role
            $connection->executeStatement(
                "INSERT INTO player_game_role (player_id, game_role_id) VALUES (:playerId, :gameRoleId)",
                [
                    "playerId" => TestUsers::USER2_ID,
                    "gameRoleId" => "750e8400-e29b-41d4-a716-446655440001",
                ],
            );
        }

        // Crear Player para USER3 si no existe
        $player3Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM player WHERE id = :id",
            ["id" => TestUsers::USER3_ID],
        );

        if ($player3Exists == 0) {
            $connection->executeStatement(
                "INSERT INTO player (id, user_id, game_rank_id, username, verified, created_at)
                 VALUES (:id, :userId, :gameRankId, :username, :verified, NOW())",
                [
                    "id" => TestUsers::USER3_ID,
                    "userId" => TestUsers::USER3_ID,
                    "gameRankId" => "850e8400-e29b-41d4-a716-446655440011",
                    "username" => "bob",
                    "verified" => 0,
                ],
            );

            // Crear relación player_game_role
            $connection->executeStatement(
                "INSERT INTO player_game_role (player_id, game_role_id) VALUES (:playerId, :gameRoleId)",
                [
                    "playerId" => TestUsers::USER3_ID,
                    "gameRoleId" => "750e8400-e29b-41d4-a716-446655440001",
                ],
            );
        }

        // Verificar si el juego ya existe
        $gameExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game WHERE id = :id",
            ["id" => self::TEST_GAME_ID],
        );

        if (!$gameExists) {
            // Crear juego de test
            $game = new Game(
                new Uuid(self::TEST_GAME_ID),
                "League of Legends",
                "MOBA game",
                5,
                5,
            );
            $this->entityManager->persist($game);
            $this->entityManager->flush();
        }

        // Verificar si el segundo juego ya existe
        $game2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game WHERE id = :id",
            ["id" => self::TEST_GAME_ID_2],
        );

        if (!$game2Exists) {
            // Crear segundo juego de test
            $game2 = new Game(
                new Uuid(self::TEST_GAME_ID_2),
                "Valorant",
                "FPS game",
                5,
                5,
            );
            $this->entityManager->persist($game2);
            $this->entityManager->flush();
        }

        // Verificar si el equipo ya existe
        $teamExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM team WHERE id = :id",
            ["id" => self::TEST_TEAM_ID],
        );

        if (!$teamExists) {
            // Crear equipo de test
            $team = Team::create(
                new Uuid(self::TEST_TEAM_ID),
                new TeamNameValue("Test Gaming Team"),
                new TeamDescriptionValue("Test team description"),
                new TeamImageValue("https://example.com/team-image.png"),
                $user,
            );
            $this->entityManager->persist($team);
            $this->entityManager->flush();
        }

        // Verificar si el segundo equipo ya existe
        $team2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM team WHERE id = :id",
            ["id" => self::TEST_TEAM_ID_2],
        );

        if (!$team2Exists) {
            // Crear segundo equipo de test para delete
            $team2 = Team::create(
                new Uuid(self::TEST_TEAM_ID_2),
                new TeamNameValue("Team to Delete"),
                new TeamDescriptionValue("Team to be deleted"),
                new TeamImageValue("https://example.com/team-image-2.png"),
                $user,
            );
            $this->entityManager->persist($team2);
            $this->entityManager->flush();
        }

        // Crear relación team_game para los tests de eliminación
        // Siempre eliminar todas las relaciones team_game y recrear el estado inicial
        try {
            $connection->executeStatement("DELETE FROM team_game");
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // Crear la relación team_game con un UUID (estado inicial para tests)
        // Usar TEST_TEAM_ID_2 que es el que se usa en los tests (550e8400-e29b-41d4-a716-446655440060)
        $teamGameId = "650e8400-e29b-41d4-a716-446655440001";
        $connection->executeStatement(
            "INSERT INTO team_game (id, team_id, game_id, added_at) VALUES (:id, :teamId, :gameId, NOW())",
            [
                "id" => $teamGameId,
                "teamId" => self::TEST_TEAM_ID_2,
                "gameId" => self::TEST_GAME_ID_2,
            ],
        );
    }

    /** @AfterScenario @team */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // 1. Limpiar team_requests de todos los equipos
            $connection->executeStatement("DELETE FROM team_request");
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 2. Limpiar team_players de todos los equipos
            $connection->executeStatement("DELETE FROM team_player");
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 3. NO limpiar team_game - se maneja en BeforeScenario
            // Solo limpiar team_game que no sean de test base
            // El BeforeScenario se encarga de recrear el estado inicial
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 4. Limpiar SOLO equipos creados en tests de create, NO los de test base
            $teamsToDelete = [
                "550e8400-e29b-41d4-a716-446655440070", // team_create.feature:7
                "550e8400-e29b-41d4-a716-446655440075", // team_create.feature:20
                "550e8400-e29b-41d4-a716-446655440076", // team_create.feature:33
                "750e8400-e29b-41d4-a716-446655440001", // team_requests.feature teams
                "750e8400-e29b-41d4-a716-446655440002",
                "750e8400-e29b-41d4-a716-446655440003",
            ];

            foreach ($teamsToDelete as $teamId) {
                // Primero eliminar dependencias
                $connection->executeStatement(
                    "DELETE FROM team_player WHERE team_id = :id",
                    ["id" => $teamId]
                );
                $connection->executeStatement(
                    "DELETE FROM match_participant WHERE team_id = :id",
                    ["id" => $teamId]
                );
                $connection->executeStatement(
                    "DELETE FROM tournament_team WHERE team_id = :id",
                    ["id" => $teamId]
                );
                $connection->executeStatement(
                    "DELETE FROM tournament_request WHERE team_id = :id",
                    ["id" => $teamId]
                );
                // Luego el team
                $connection->executeStatement(
                    "DELETE FROM team WHERE id = :id",
                    ["id" => $teamId]
                );
            }
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 5. NO limpiar juegos - se crean en BeforeScenario y deben persistir
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 6. Limpiar segundo juego de prueba
            $connection->executeStatement(
                "DELETE FROM game WHERE id = :gameId",
                ["gameId" => self::TEST_GAME_ID_2],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // NO eliminar el usuario - es compartido entre contextos
        // Limpiar el entity manager
        $this->entityManager->clear();
    }

    private function createPlayerDependencies(Connection $connection): void
    {
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
    }
}
