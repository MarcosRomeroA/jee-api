<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Team;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class TeamTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @team */
    public function createTestData(): void
    {
        $userId = new Uuid('550e8400-e29b-41d4-a716-446655440001');
        $gameId = new Uuid('550e8400-e29b-41d4-a716-446655440002');
        $teamId = new Uuid('550e8400-e29b-41d4-a716-446655440060');

        // Verificar si el usuario ya existe
        $existingUser = $this->entityManager->find(User::class, $userId);
        if (!$existingUser) {
            // Crear usuario de test
            // IMPORTANTE: PasswordValue hashea automáticamente, así que pasamos el plaintext
            $user = User::create(
                $userId,
                new FirstnameValue('John'),
                new LastnameValue('Doe'),
                new UsernameValue('testuser'),
                new EmailValue('test@example.com'),
                new PasswordValue('password123')
            );
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else {
            $user = $existingUser;
        }

        // Verificar si el juego ya existe
        $existingGame = $this->entityManager->find(Game::class, $gameId);
        if (!$existingGame) {
            $game = new Game(
                $gameId,
                'League of Legends',
                'MOBA game',
                5,
                5
            );
            $this->entityManager->persist($game);
            $this->entityManager->flush();
        } else {
            $game = $existingGame;
        }

        // Limpiar equipos antiguos antes de crear uno nuevo
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Team\Domain\Team t WHERE t.id = :teamId')
            ->setParameter('teamId', $teamId->value())
            ->execute();

        // Crear equipo de test
        $team = new Team(
            $teamId,
            $game,
            $user,
            'Test Gaming Team',
            'https://example.com/team-image.png'
        );
        $this->entityManager->persist($team);
        $this->entityManager->flush();
    }

    /** @AfterScenario @team */
    public function cleanupTestData(): void
    {
        // Limpiar en orden inverso respetando las claves foráneas

        // 1. Limpiar team_players primero
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Team\Domain\TeamPlayer')->execute();

        // 2. Limpiar team_requests
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Team\Domain\TeamRequest')->execute();

        // 3. Limpiar equipos
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Team\Domain\Team')->execute();

        // NO eliminamos usuarios ni games porque pueden tener otras dependencias (messages, posts, etc)
        // Los datos de test se mantienen entre escenarios

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}

