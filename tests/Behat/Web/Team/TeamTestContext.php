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
        // Crear un usuario de prueba (owner)
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

        // Crear un equipo de prueba
        $team = new Team(
            new Uuid('550e8400-e29b-41d4-a716-446655440060'),
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
        // Limpiar equipos (esto también limpiará team_players por cascade)
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Team\Domain\Team')->execute();

        // Limpiar juegos
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Game\Domain\Game')->execute();

        // Limpiar usuarios
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\User\Domain\User')->execute();
    }
}

