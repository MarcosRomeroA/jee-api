<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Team;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Team\Domain\Team;
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
    ) {}

    /** @BeforeScenario @team */
    public function createTestData(): void
    {
        // Obtener o crear el usuario compartido
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

        // Verificar si el juego ya existe
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

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
        } else {
            // Obtener el juego existente
            $game = $this->entityManager
                ->getRepository(Game::class)
                ->find(self::TEST_GAME_ID);
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
                "Test Gaming Team",
                "Test team description",
                "https://example.com/team-image.png",
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
                "Team to Delete",
                "Team to be deleted",
                "https://example.com/team-image-2.png",
                $user,
            );
            $this->entityManager->persist($team2);
            $this->entityManager->flush();
        }
    }

    /** @AfterScenario @team */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // 1. Limpiar team_players del equipo de prueba con SQL nativo
            $connection->executeStatement(
                "DELETE FROM team_player WHERE team_id = :teamId",
                ["teamId" => self::TEST_TEAM_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 2. Limpiar team_requests del equipo de prueba
            $connection->executeStatement(
                "DELETE FROM team_request WHERE team_id = :teamId",
                ["teamId" => self::TEST_TEAM_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 3. Limpiar equipo de prueba
            $connection->executeStatement(
                "DELETE FROM team WHERE id = :teamId",
                ["teamId" => self::TEST_TEAM_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 4. Limpiar segundo equipo de prueba
            $connection->executeStatement(
                "DELETE FROM team WHERE id = :teamId",
                ["teamId" => self::TEST_TEAM_ID_2],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 5. Limpiar equipos del usuario de prueba (creados dinámicamente)
            $connection->executeStatement(
                "DELETE FROM team WHERE creator_id = :userId AND id NOT IN (:excludeId1, :excludeId2)",
                [
                    "userId" => TestUsers::USER1_ID,
                    "excludeId1" => self::TEST_TEAM_ID,
                    "excludeId2" => self::TEST_TEAM_ID_2,
                ],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 5. Limpiar juego de prueba
            $connection->executeStatement(
                "DELETE FROM game WHERE id = :gameId",
                ["gameId" => self::TEST_GAME_ID],
            );
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
}
