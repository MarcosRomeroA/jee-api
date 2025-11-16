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
        // Los usuarios globales ya fueron creados en DatabaseContext::setupDatabase()
        // No necesitamos crear usuarios aquí
        $this->entityManager->clear();
    }

    /** @AfterScenario */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Limpiar SOLO relaciones de seguimiento de usuarios de prueba
            // NO eliminar usuarios, otros contextos los necesitan
            $connection->executeStatement(
                "DELETE FROM user_follow WHERE follower_id IN (:id1, :id2) OR followed_id IN (:id3, :id4)",
                [
                    "id1" => TestUsers::USER1_ID,
                    "id2" => TestUsers::USER2_ID,
                    "id3" => TestUsers::USER1_ID,
                    "id4" => TestUsers::USER2_ID,
                ],
            );

            // Restaurar emails originales de los usuarios de prueba
            // NO actualizar la contraseña para evitar cambiar el hash
            $connection->executeStatement(
                "UPDATE user SET email = :email1, username = :username1, firstname = :firstname1, lastname = :lastname1 WHERE id = :id1",
                [
                    "id1" => TestUsers::USER1_ID,
                    "email1" => "test@example.com",
                    "username1" => "testuser",
                    "firstname1" => "Test",
                    "lastname1" => "User",
                ]
            );
            $connection->executeStatement(
                "UPDATE user SET email = :email2, username = :username2, firstname = :firstname2, lastname = :lastname2 WHERE id = :id2",
                [
                    "id2" => TestUsers::USER2_ID,
                    "email2" => "jane@example.com",
                    "username2" => "janesmith",
                    "firstname2" => "Jane",
                    "lastname2" => "Smith",
                ]
            );
            $connection->executeStatement(
                "UPDATE user SET email = :email3, username = :username3, firstname = :firstname3, lastname = :lastname3 WHERE id = :id3",
                [
                    "id3" => TestUsers::USER3_ID,
                    "email3" => "bob@example.com",
                    "username3" => "bobtest",
                    "firstname3" => "Bob",
                    "lastname3" => "Test",
                ]
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // Limpiar el entity manager
        $this->entityManager->clear();
    }
}
