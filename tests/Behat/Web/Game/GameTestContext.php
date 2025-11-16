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
        // Limpiar caché antes de verificar
        $this->entityManager->clear();

        // Crear juegos de prueba solo si no existen
        $game1Id = new Uuid("550e8400-e29b-41d4-a716-446655440080");
        $existingGame1 = $this->entityManager->find(Game::class, $game1Id);

        if (!$existingGame1) {
            $game1 = new Game(
                $game1Id,
                "Valorant",
                "Tactical first-person shooter developed by Riot Games",
                5,
                5,
            );
            $this->entityManager->persist($game1);
        }

        $game2Id = new Uuid("550e8400-e29b-41d4-a716-446655440081");
        $existingGame2 = $this->entityManager->find(Game::class, $game2Id);

        if (!$existingGame2) {
            $game2 = new Game(
                $game2Id,
                "League of Legends",
                "Multiplayer online battle arena game developed by Riot Games",
                5,
                5,
            );
            $this->entityManager->persist($game2);
        }

        $game3Id = new Uuid("550e8400-e29b-41d4-a716-446655440082");
        $existingGame3 = $this->entityManager->find(Game::class, $game3Id);

        if (!$existingGame3) {
            $game3 = new Game(
                $game3Id,
                "Counter-Strike 2",
                "Tactical first-person shooter developed by Valve",
                5,
                5,
            );
            $this->entityManager->persist($game3);
        }

        $game4Id = new Uuid("550e8400-e29b-41d4-a716-446655440083");
        $existingGame4 = $this->entityManager->find(Game::class, $game4Id);

        if (!$existingGame4) {
            $game4 = new Game(
                $game4Id,
                "Dota 2",
                "Multiplayer online battle arena game developed by Valve",
                5,
                5,
            );
            $this->entityManager->persist($game4);
        }

        $this->entityManager->flush();
    }

    /** @AfterScenario @game */
    public function cleanupTestData(): void
    {
        // No eliminar los juegos porque están en las migraciones y tienen relaciones
        // con game_rank y game_role que se usan en los tests

        // Solo limpiar caché del EntityManager
        $this->entityManager->clear();
    }
}
