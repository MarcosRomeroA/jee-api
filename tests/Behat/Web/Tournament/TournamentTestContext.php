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
        // Crear un usuario de prueba (responsible)
        $user = new User(
            new Uuid('550e8400-e29b-41d4-a716-446655440001'),
            new FirstnameValue('John'),
            new LastnameValue('Doe'),
            new UsernameValue('testuser'),
            new EmailValue('test@example.com'),
            new PasswordValue(password_hash('password123', PASSWORD_BCRYPT))
        );
        $this->entityManager->persist($user);

        // Crear un juego de prueba
        $game = new Game(
            new Uuid('550e8400-e29b-41d4-a716-446655440002'),
            'League of Legends',
            'MOBA game',
            5,
            5
        );
        $this->entityManager->persist($game);

        // Crear rangos de juego
        $minRank = new GameRank(
            new Uuid('550e8400-e29b-41d4-a716-446655440003'),
            $game,
            'Silver',
            3
        );
        $this->entityManager->persist($minRank);

        $maxRank = new GameRank(
            new Uuid('550e8400-e29b-41d4-a716-446655440004'),
            $game,
            'Diamond',
            7
        );
        $this->entityManager->persist($maxRank);

        // Crear estado de torneo
        $status = new TournamentStatus(
            'active',
            'Active Tournament'
        );
        $this->entityManager->persist($status);

        // Crear un torneo de prueba
        $tournament = new Tournament(
            new Uuid('550e8400-e29b-41d4-a716-446655440070'),
            $game,
            $status,
            $user,
            'Test Summer Championship',
            'This is a test tournament for competitive gaming',
            16,
            true,
            'https://example.com/tournament-image.png',
            '10000 USD',
            'NA',
            new \DateTimeImmutable('+1 week'),
            new \DateTimeImmutable('+2 weeks'),
            $minRank,
            $maxRank
        );
        $this->entityManager->persist($tournament);

        $this->entityManager->flush();
    }

    /** @AfterScenario @tournament */
    public function cleanupTestData(): void
    {
        // Limpiar torneos (esto también limpiará tournament_teams por cascade)
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Tournament\Domain\Tournament')->execute();

        // Limpiar estados de torneo
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Tournament\Domain\TournamentStatus')->execute();

        // Limpiar rangos de juego
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Game\Domain\GameRank')->execute();

        // Limpiar juegos
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Game\Domain\Game')->execute();

        // Limpiar usuarios
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\User\Domain\User')->execute();
    }
}

