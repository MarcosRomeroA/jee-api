<?php declare(strict_types=1);

namespace App\Tests\Apps\Web\Player;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\Role;
use App\Contexts\Web\User\Domain\User;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class PlayerTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @player */
    public function createTestData(): void
    {
        // Crear un juego de prueba (League of Legends)
        $game = new Game(
            new Uuid('550e8400-e29b-41d4-a716-446655440002'),
            'League of Legends',
            'MOBA game',
            5,
            5
        );
        $this->entityManager->persist($game);

        // Crear un role genérico
        $role = new Role(
            new Uuid('550e8400-e29b-41d4-a716-446655440010'),
            'Mid Laner',
            'Middle lane player'
        );
        $this->entityManager->persist($role);

        // Crear un GameRole
        $gameRole = new GameRole(
            new Uuid('550e8400-e29b-41d4-a716-446655440003'),
            $role,
            $game
        );
        $this->entityManager->persist($gameRole);

        // Crear otro GameRole para update
        $gameRole2 = new GameRole(
            new Uuid('550e8400-e29b-41d4-a716-446655440005'),
            $role,
            $game
        );
        $this->entityManager->persist($gameRole2);

        // Crear un GameRank
        $gameRank = new GameRank(
            new Uuid('550e8400-e29b-41d4-a716-446655440004'),
            $game,
            'Gold',
            5
        );
        $this->entityManager->persist($gameRank);

        // Crear otro GameRank para update
        $gameRank2 = new GameRank(
            new Uuid('550e8400-e29b-41d4-a716-446655440006'),
            $game,
            'Platinum',
            6
        );
        $this->entityManager->persist($gameRank2);

        // Crear un usuario de prueba
        $user = new User(
            new Uuid('550e8400-e29b-41d4-a716-446655440001'),
            'John',
            'Doe',
            'testuser',
            'test@example.com',
            password_hash('password123', PASSWORD_BCRYPT)
        );
        $this->entityManager->persist($user);

        $this->entityManager->flush();
    }

    /** @AfterScenario @player */
    public function cleanupTestData(): void
    {
        // Limpiar players
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Player\Domain\Player')->execute();

        // Limpiar datos de prueba
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Game\Domain\GameRank')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Game\Domain\GameRole')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Game\Domain\Role')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Game\Domain\Game')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\User\Domain\User')->execute();
    }
}

