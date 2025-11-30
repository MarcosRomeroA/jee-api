<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\Team;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\ValueObject\TeamNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamImageValue;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentTeam;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class TeamTestContext implements Context
{
    private const TEST_GAME_ID = "550e8400-e29b-41d4-a716-446655440071";
    private const TEST_GAME_ID_2 = "550e8400-e29b-41d4-a716-446655440002";
    private const TEST_TEAM_ID = "550e8400-e29b-41d4-a716-446655440072";
    private const TEST_TEAM_ID_2 = "550e8400-e29b-41d4-a716-446655440060";

    // IDs de datos creados por ESTE contexto que deben limpiarse
    private array $createdGameIds = [];
    private array $createdPlayerIds = [];
    private array $createdTeamIds = [];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /** @BeforeScenario @team */
    public function createTestData(): void
    {
        // Resetear arrays de tracking
        $this->createdGameIds = [];
        $this->createdPlayerIds = [];
        $this->createdTeamIds = [];

        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // IMPORTANTE: Limpiar team_requests ANTES de cada escenario para evitar contaminación
        try {
            $connection->executeStatement("DELETE FROM team_request");
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // Obtener el primer usuario disponible (creado por el test via DatabaseContext)
        $user = $connection->fetchOne("SELECT id FROM user ORDER BY created_at ASC LIMIT 1");

        if (!$user) {
            // Si no hay usuario, no crear datos de test
            return;
        }

        $user = $this->userRepository->findById(new Uuid($user));

        // Los datos de referencia (games, roles, ranks, game_roles, game_ranks)
        // ya existen en la base de datos creados por la migración Version20251116054200
        // Sin embargo, necesitamos crear algunos players y teams específicos para los tests

        // Crear Players si no existen (usando datos de migración existentes)
        $this->createPlayers($connection);

        // Crear Games adicionales para tests de Team (no están en migración)
        $this->createGames($connection);

        // Crear Teams para tests
        $this->createTeams($connection, $user);

        // Crear relación team_game inicial
        $this->createTeamGame($connection);
    }

    private function createPlayers(Connection $connection): void
    {
        // Usar game_rank de la migración: Valorant Gold 2 (ID: 850e8400-e29b-41d4-a716-446655440011)
        // Usar game_role de la migración: Valorant Duelist (ID: 750e8400-e29b-41d4-a716-446655440001)

        // Obtener usuarios creados dinámicamente por el test
        $users = $connection->fetchAllAssociative(
            "SELECT id, username FROM user ORDER BY created_at ASC"
        );

        // Crear players para cada usuario si no existen
        foreach ($users as $userData) {
            $userId = $userData['id'];
            $username = $userData['username'];

            $playerExists = $connection->fetchOne(
                "SELECT COUNT(*) FROM player WHERE id = :id",
                ["id" => $userId],
            );

            if ($playerExists == 0) {
                $connection->executeStatement(
                    "INSERT INTO player (id, user_id, game_rank_id, username, verified, created_at)
                     VALUES (:id, :userId, :gameRankId, :username, :verified, NOW())",
                    [
                        "id" => $userId,
                        "userId" => $userId,
                        "gameRankId" => "850e8400-e29b-41d4-a716-446655440011", // Valorant Gold 2 (migración)
                        "username" => $username,
                        "verified" => 0,
                    ],
                );

                // Crear relación player_game_role
                $connection->executeStatement(
                    "INSERT INTO player_game_role (player_id, game_role_id) VALUES (:playerId, :gameRoleId)",
                    [
                        "playerId" => $userId,
                        "gameRoleId" => "750e8400-e29b-41d4-a716-446655440001", // Valorant Duelist (migración)
                    ],
                );

                $this->createdPlayerIds[] = $userId;
            }
        }
    }

    private function createGames(Connection $connection): void
    {
        // Crear primer juego de test si no existe (NO está en migración)
        $gameExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game WHERE id = :id",
            ["id" => self::TEST_GAME_ID],
        );

        if (!$gameExists) {
            $game = new Game(
                new Uuid(self::TEST_GAME_ID),
                "League of Legends Test",
                "MOBA game for testing",
                5,
                5,
            );
            $this->entityManager->persist($game);
            $this->entityManager->flush();
            $this->createdGameIds[] = self::TEST_GAME_ID;
        }

        // Crear segundo juego de test si no existe (NO está en migración)
        $game2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM game WHERE id = :id",
            ["id" => self::TEST_GAME_ID_2],
        );

        if (!$game2Exists) {
            $game2 = new Game(
                new Uuid(self::TEST_GAME_ID_2),
                "Valorant Test",
                "FPS game for testing",
                5,
                5,
            );
            $this->entityManager->persist($game2);
            $this->entityManager->flush();
            $this->createdGameIds[] = self::TEST_GAME_ID_2;
        }
    }

    private function createTeams(Connection $connection, User $user): void
    {
        // Crear primer equipo de test si no existe
        $teamExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM team WHERE id = :id",
            ["id" => self::TEST_TEAM_ID],
        );

        if (!$teamExists) {
            $team = Team::create(
                new Uuid(self::TEST_TEAM_ID),
                new TeamNameValue("Test Gaming Team"),
                new TeamDescriptionValue("Test team description"),
                new TeamImageValue(null),
                $user,
            );
            $this->entityManager->persist($team);
            $this->entityManager->flush();
            $this->createdTeamIds[] = self::TEST_TEAM_ID;
        }

        // Crear segundo equipo de test para delete si no existe
        $team2Exists = $connection->fetchOne(
            "SELECT COUNT(*) FROM team WHERE id = :id",
            ["id" => self::TEST_TEAM_ID_2],
        );

        if (!$team2Exists) {
            $team2 = Team::create(
                new Uuid(self::TEST_TEAM_ID_2),
                new TeamNameValue("Team to Delete"),
                new TeamDescriptionValue("Team to be deleted"),
                new TeamImageValue(null),
                $user,
            );
            $this->entityManager->persist($team2);
            $this->entityManager->flush();
            $this->createdTeamIds[] = self::TEST_TEAM_ID_2;
        }
    }

    private function createTeamGame(Connection $connection): void
    {
        // Crear relación team_game para los tests de eliminación
        // Siempre eliminar todas las relaciones team_game y recrear el estado inicial
        try {
            $connection->executeStatement("DELETE FROM team_game");
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // Crear la relación team_game con un UUID (estado inicial para tests)
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
            // 1. Limpiar team_requests
            $connection->executeStatement("DELETE FROM team_request");
        } catch (\Exception $e) {
        }

        try {
            // 2. Limpiar team_players (deprecado)
            $connection->executeStatement("DELETE FROM team_player");
        } catch (\Exception $e) {
        }

        try {
            // 2b. Limpiar team_user (nueva tabla de membresía)
            $connection->executeStatement("DELETE FROM team_user");
        } catch (\Exception $e) {
        }

        try {
            // 3. Limpiar team_game
            $connection->executeStatement("DELETE FROM team_game");
        } catch (\Exception $e) {
        }

        // 4. Limpiar equipos creados por ESTE contexto
        if (!empty($this->createdTeamIds)) {
            try {
                $placeholders = implode(',', array_map(fn ($id) => "'$id'", $this->createdTeamIds));
                $connection->executeStatement(
                    "DELETE FROM team WHERE id IN ({$placeholders})"
                );
            } catch (\Exception $e) {
            }
        }

        // Limpiar también equipos con IDs conocidos de tests (por si no están en el array)
        $additionalTeamIds = [
            "550e8400-e29b-41d4-a716-446655440070",
            "550e8400-e29b-41d4-a716-446655440075",
            "550e8400-e29b-41d4-a716-446655440076",
            "750e8400-e29b-41d4-a716-446655440001",
            "750e8400-e29b-41d4-a716-446655440002",
            "750e8400-e29b-41d4-a716-446655440003",
        ];

        foreach ($additionalTeamIds as $teamId) {
            try {
                $connection->executeStatement(
                    "DELETE FROM team WHERE id = :id",
                    ["id" => $teamId]
                );
            } catch (\Exception $e) {
            }
        }

        // 5. Limpiar players creados por ESTE contexto
        if (!empty($this->createdPlayerIds)) {
            try {
                // Primero limpiar player_game_role
                $placeholders = implode(',', array_map(fn ($id) => "'$id'", $this->createdPlayerIds));
                $connection->executeStatement(
                    "DELETE FROM player_game_role WHERE player_id IN ({$placeholders})"
                );

                // Luego limpiar players
                $connection->executeStatement(
                    "DELETE FROM player WHERE id IN ({$placeholders})"
                );
            } catch (\Exception $e) {
            }
        }

        // 6. Limpiar games creados por ESTE contexto
        if (!empty($this->createdGameIds)) {
            try {
                $placeholders = implode(',', array_map(fn ($id) => "'$id'", $this->createdGameIds));
                $connection->executeStatement(
                    "DELETE FROM game WHERE id IN ({$placeholders})"
                );
            } catch (\Exception $e) {
            }
        }

        // NO limpiar datos de migración:
        // - games de migración (550e8400-e29b-41d4-a716-446655440080-083)
        // - roles de migración (650e8400-e29b-41d4-a716-446655440001-019)
        // - ranks de migración (950e8400-e29b-41d4-a716-446655440001-036)
        // - game_roles de migración (750e8400-e29b-41d4-a716-446655440001-019)
        // - game_ranks de migración (850e8400-e29b-41d4-a716-446655440001-335)
        // Estos son datos de referencia estáticos creados por Version20251116054200
        // y NUNCA deben eliminarse

        // 7. Resetear arrays de tracking
        $this->createdGameIds = [];
        $this->createdPlayerIds = [];
        $this->createdTeamIds = [];

        $this->entityManager->clear();
    }

    /**
     * @Given a team :teamId exists with name :teamName
     */
    public function aTeamExistsWithName(string $teamId, string $teamName): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        $user = $connection->fetchOne("SELECT id FROM user ORDER BY created_at ASC LIMIT 1");

        if (!$user) {
            throw new \RuntimeException("No user found for creating team");
        }

        $userEntity = $this->userRepository->findById(new Uuid($user));

        $teamExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM team WHERE id = :id",
            ["id" => $teamId],
        );

        if (!$teamExists) {
            $team = Team::create(
                new Uuid($teamId),
                new TeamNameValue($teamName),
                new TeamDescriptionValue("Test team for tournament filter"),
                new TeamImageValue(null),
                $userEntity,
            );
            $this->entityManager->persist($team);
            $this->entityManager->flush();
            $this->createdTeamIds[] = $teamId;
        }
    }

    /**
     * @Given a team :teamId exists with name :teamName created by :email
     */
    public function aTeamExistsWithNameCreatedBy(string $teamId, string $teamName, string $email): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        $userId = $connection->fetchOne(
            "SELECT id FROM user WHERE email = :email",
            ["email" => $email]
        );

        if (!$userId) {
            throw new \RuntimeException("User with email $email not found");
        }

        $userEntity = $this->userRepository->findById(new Uuid($userId));

        $teamExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM team WHERE id = :id",
            ["id" => $teamId],
        );

        if (!$teamExists) {
            $team = Team::create(
                new Uuid($teamId),
                new TeamNameValue($teamName),
                new TeamDescriptionValue("Test team"),
                new TeamImageValue(null),
                $userEntity,
            );
            $this->entityManager->persist($team);
            $this->entityManager->flush();
            $this->createdTeamIds[] = $teamId;
        }
    }

    /**
     * @Given the team :teamId is registered in tournament :tournamentId
     */
    public function theTeamIsRegisteredInTournament(string $teamId, string $tournamentId): void
    {
        $team = $this->entityManager->find(Team::class, new Uuid($teamId));
        $tournament = $this->entityManager->find(Tournament::class, new Uuid($tournamentId));

        if (!$team) {
            throw new \RuntimeException("Team with id $teamId not found");
        }

        if (!$tournament) {
            throw new \RuntimeException("Tournament with id $tournamentId not found");
        }

        $tournamentTeam = new TournamentTeam(
            Uuid::random(),
            $tournament,
            $team,
        );

        $this->entityManager->persist($tournamentTeam);
        $this->entityManager->flush();
    }

    /**
     * @Given user :email is the leader of team :teamId
     */
    public function userIsTheLeaderOfTeam(string $email, string $teamId): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // Find user by email
        $userId = $connection->fetchOne(
            "SELECT id FROM user WHERE email = :email",
            ["email" => $email]
        );

        if (!$userId) {
            throw new \RuntimeException("User with email $email not found");
        }

        $team = $this->entityManager->find(Team::class, new Uuid($teamId));

        if (!$team) {
            throw new \RuntimeException("Team with id $teamId not found");
        }

        $user = $this->userRepository->findById(new Uuid($userId));
        $team->setLeader($user);

        $this->entityManager->flush();
    }
}
