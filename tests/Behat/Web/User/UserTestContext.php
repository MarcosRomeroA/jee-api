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
use App\Tests\Behat\Shared\Fixtures\TestUsers;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class UserTestContext implements Context
{
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
                    "id1" => TestUsers::USER1_ID,
                    "id2" => TestUsers::USER2_ID,
                    "id3" => TestUsers::USER3_ID,
                    "id4" => TestUsers::USER1_ID,
                    "id5" => TestUsers::USER2_ID,
                    "id6" => TestUsers::USER3_ID,
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
