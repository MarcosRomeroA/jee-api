<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\Player;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class PlayerTestContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /** @BeforeScenario @player */
    public function createTestData(): void
    {
        // Los datos de referencia (games, roles, ranks, game_roles, game_ranks)
        // ya existen en la base de datos creados por la migración Version20251116054200
        // NO necesitamos crearlos aquí.

        $this->entityManager->clear();
    }

    /** @AfterScenario @player */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // ID del usuario tester1 (de la migración Version20251119000001)
        $tester1Id = '550e8400-e29b-41d4-a716-446655440001';

        try {
            // 1. Limpiar player_game_role del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM player_game_role WHERE player_id IN (SELECT id FROM player WHERE user_id = :userId)",
                ["userId" => $tester1Id],
            );

            // 2. Limpiar players del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM player WHERE user_id = :userId",
                ["userId" => $tester1Id],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 3. Limpiar player_game_role de players con usernames de prueba
            $connection->executeStatement(
                "DELETE FROM player_game_role WHERE player_id IN (SELECT id FROM player WHERE username IN ('ProGamer123', 'TestPlayer1', 'UpdatedUsername', 'OriginalName', 'UpdatedName'))"
            );

            // 4. Limpiar players con usernames de prueba específicos
            $connection->executeStatement(
                "DELETE FROM player WHERE username IN ('ProGamer123', 'TestPlayer1', 'UpdatedUsername', 'OriginalName', 'UpdatedName')"
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // 5. Limpiar player_game_role de players con IDs de prueba
            $connection->executeStatement(
                "DELETE FROM player_game_role WHERE player_id IN (
                    '550e8400-e29b-41d4-a716-446655440100',
                    '550e8400-e29b-41d4-a716-446655440101',
                    '550e8400-e29b-41d4-a716-446655440102',
                    '550e8400-e29b-41d4-a716-446655440103',
                    '550e8400-e29b-41d4-a716-446655440104',
                    '550e8400-e29b-41d4-a716-446655440300',
                    '550e8400-e29b-41d4-a716-446655440400'
                )"
            );

            // 6. Limpiar players con IDs de prueba específicos
            $connection->executeStatement(
                "DELETE FROM player WHERE id IN (
                    '550e8400-e29b-41d4-a716-446655440100',
                    '550e8400-e29b-41d4-a716-446655440101',
                    '550e8400-e29b-41d4-a716-446655440102',
                    '550e8400-e29b-41d4-a716-446655440103',
                    '550e8400-e29b-41d4-a716-446655440104',
                    '550e8400-e29b-41d4-a716-446655440300',
                    '550e8400-e29b-41d4-a716-446655440400'
                )"
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // NO limpiar datos de migración:
        // - games, roles, ranks, game_roles, game_ranks
        // Estos son datos de referencia estáticos creados por Version20251116054200
        // y NUNCA deben eliminarse

        $this->entityManager->clear();
    }
}
