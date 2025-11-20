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
        // Los juegos ya vienen de la migración, solo limpiar caché
        $this->entityManager->clear();
    }

    /** @AfterScenario @game */
    public function cleanupTestData(): void
    {
        // Los juegos vienen de la migración y son datos de referencia
        // NO deben eliminarse
        $this->entityManager->clear();
    }
}
