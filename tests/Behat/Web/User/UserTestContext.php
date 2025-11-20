<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\User;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class UserTestContext implements Context
{
    // IDs de usuarios de la migración Version20251119000001
    private const USER1_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const USER2_ID = '550e8400-e29b-41d4-a716-446655440002';
    private const USER3_ID = '550e8400-e29b-41d4-a716-446655440003';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /** @BeforeScenario @user */
    public function createTestData(): void
    {
        // Los usuarios estáticos (tester1, tester2, tester3) ya existen en la base de datos
        // Son creados por la migración Version20251119000001 y NO deben ser modificados
        $this->entityManager->clear();
    }

    /** @AfterScenario @user */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Limpiar SOLO relaciones de seguimiento creadas durante los tests
            // Los usuarios estáticos NO se modifican, solo sus relaciones temporales
            $connection->executeStatement(
                "DELETE FROM user_follow WHERE follower_id IN (:id1, :id2, :id3) OR followed_id IN (:id4, :id5, :id6)",
                [
                    "id1" => self::USER1_ID,
                    "id2" => self::USER2_ID,
                    "id3" => self::USER3_ID,
                    "id4" => self::USER1_ID,
                    "id5" => self::USER2_ID,
                    "id6" => self::USER3_ID,
                ],
            );

            // NO restaurar datos de usuarios - son estáticos y no deben modificarse
            // Si un test necesita modificar un usuario, debe crear uno temporal
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // Limpiar el entity manager
        $this->entityManager->clear();
    }
}
