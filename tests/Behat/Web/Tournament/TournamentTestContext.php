<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Tournament;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentStatus;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class TournamentTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @tournament */
    public function createTestData(): void
    {
        // Limpiar caché antes de verificar
        $this->entityManager->clear();

        // Usar el usuario de autenticación compartido (test@example.com)
        $userId = new Uuid("550e8400-e29b-41d4-a716-446655440001");
        $existingUser = $this->entityManager->find(User::class, $userId);

        if (!$existingUser) {
            $user = new User(
                $userId,
                new FirstnameValue("Test"),
                new LastnameValue("User"),
                new UsernameValue("testuser"),
                new EmailValue("test@example.com"),
                new PasswordValue(
                    password_hash("password123", PASSWORD_BCRYPT),
                ),
            );
            $this->entityManager->persist($user);
        } else {
            $user = $existingUser;
        }

        // Crear un juego de prueba
        $gameId = new Uuid("750e8400-e29b-41d4-a716-446655440001");
        $existingGame = $this->entityManager->find(Game::class, $gameId);

        if (!$existingGame) {
            $game = new Game($gameId, "League of Legends", "MOBA game", 5, 5);
            $this->entityManager->persist($game);
        } else {
            $game = $existingGame;
        }

        // Crear rangos
        $silverRankId = new Uuid("750e8400-e29b-41d4-a716-446655440005");
        $existingSilverRank = $this->entityManager->find(
            \App\Contexts\Web\Game\Domain\Rank::class,
            $silverRankId,
        );

        if (!$existingSilverRank) {
            $silverRank = new \App\Contexts\Web\Game\Domain\Rank(
                $silverRankId,
                "Silver",
            );
            $this->entityManager->persist($silverRank);
        } else {
            $silverRank = $existingSilverRank;
        }

        $diamondRankId = new Uuid("750e8400-e29b-41d4-a716-446655440006");
        $existingDiamondRank = $this->entityManager->find(
            \App\Contexts\Web\Game\Domain\Rank::class,
            $diamondRankId,
        );

        if (!$existingDiamondRank) {
            $diamondRank = new \App\Contexts\Web\Game\Domain\Rank(
                $diamondRankId,
                "Diamond",
            );
            $this->entityManager->persist($diamondRank);
        } else {
            $diamondRank = $existingDiamondRank;
        }

        // Crear rangos de juego
        $minRankId = new Uuid("750e8400-e29b-41d4-a716-446655440003");
        $existingMinRank = $this->entityManager->find(
            GameRank::class,
            $minRankId,
        );

        if (!$existingMinRank) {
            $minRank = new GameRank($minRankId, $game, $silverRank, 3);
            $this->entityManager->persist($minRank);
        } else {
            $minRank = $existingMinRank;
        }

        $maxRankId = new Uuid("750e8400-e29b-41d4-a716-446655440004");
        $existingMaxRank = $this->entityManager->find(
            GameRank::class,
            $maxRankId,
        );

        if (!$existingMaxRank) {
            $maxRank = new GameRank($maxRankId, $game, $diamondRank, 7);
            $this->entityManager->persist($maxRank);
        } else {
            $maxRank = $existingMaxRank;
        }

        // Crear estado de torneo (usar el ID de "created" de la migración)
        $statusId = new Uuid("01234567-89ab-cdef-0123-000000000001");
        $existingStatus = $this->entityManager->find(
            TournamentStatus::class,
            $statusId,
        );

        if (!$existingStatus) {
            $status = new TournamentStatus($statusId, "created");
            $this->entityManager->persist($status);
        } else {
            $status = $existingStatus;
        }

        // Crear un torneo de prueba
        $tournamentId = new Uuid("750e8400-e29b-41d4-a716-446655440000");
        $existingTournament = $this->entityManager->find(
            Tournament::class,
            $tournamentId,
        );

        if (!$existingTournament) {
            $tournament = new Tournament(
                $tournamentId,
                $game,
                $status,
                $user,
                "Summer Championship 2025",
                "Annual summer tournament",
                16,
                true,
                "https://example.com/tournament-image.png",
                "10000 USD",
                "NA",
                new \DateTimeImmutable("+1 week"),
                new \DateTimeImmutable("+2 weeks"),
                $minRank,
                $maxRank,
            );
            $this->entityManager->persist($tournament);
        }

        $this->entityManager->flush();
    }

    /** @AfterScenario @tournament */
    public function cleanupTestData(): void
    {
        // Limpiar en orden inverso respetando foreign keys

        // Limpiar torneos (esto también limpiará tournament_teams por cascade)
        $this->entityManager
            ->createQuery(
                "DELETE FROM App\Contexts\Web\Tournament\Domain\Tournament",
            )
            ->execute();

        // Limpiar estados de torneo
        $this->entityManager
            ->createQuery(
                "DELETE FROM App\Contexts\Web\Tournament\Domain\TournamentStatus",
            )
            ->execute();

        // NO eliminar GameRank, Rank, GameRole ni Game porque están en las migraciones
        // y son datos compartidos que se usan en múltiples tests

        // NO eliminar usuario porque es compartido con AuthTestContext

        // Limpiar caché del EntityManager
        $this->entityManager->clear();
    }
}
