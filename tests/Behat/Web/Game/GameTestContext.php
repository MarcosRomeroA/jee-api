<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Game;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class GameTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @game */
    public function createTestData(): void
    {
        // Crear juegos de prueba
        $game1 = new Game(
            new Uuid('550e8400-e29b-41d4-a716-446655440080'),
            'League of Legends',
            'Multiplayer online battle arena game developed by Riot Games',
            5,
            5
        );
        $this->entityManager->persist($game1);

        $game2 = new Game(
            new Uuid('550e8400-e29b-41d4-a716-446655440081'),
            'Valorant',
            'Tactical first-person shooter developed by Riot Games',
            5,
            5
        );
        $this->entityManager->persist($game2);

        $game3 = new Game(
            new Uuid('550e8400-e29b-41d4-a716-446655440082'),
            'Counter-Strike 2',
            'Tactical first-person shooter developed by Valve',
            5,
            5
        );
        $this->entityManager->persist($game3);

        $this->entityManager->flush();
    }

    /** @AfterScenario @game */
    public function cleanupTestData(): void
    {
        // Limpiar juegos
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Game\Domain\Game')->execute();
    }
}

