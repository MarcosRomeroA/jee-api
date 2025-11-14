<?php declare(strict_types=1);

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
    ) {}

    /** @BeforeScenario @user */
    public function createTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // Verificar si el usuario 1 ya existe
        $existingUser1 = $connection->fetchOne(
            "SELECT COUNT(*) FROM user WHERE id = :id",
            ["id" => TestUsers::USER1_ID],
        );

        if (!$existingUser1) {
            $user1 = User::create(
                new Uuid(TestUsers::USER1_ID),
                new FirstnameValue(TestUsers::USER1_FIRSTNAME),
                new LastnameValue(TestUsers::USER1_LASTNAME),
                new UsernameValue(TestUsers::USER1_USERNAME),
                new EmailValue(TestUsers::USER1_EMAIL),
                new PasswordValue(TestUsers::USER1_PASSWORD),
            );
            $this->entityManager->persist($user1);
        }

        // Verificar si el usuario 2 ya existe
        $existingUser2 = $connection->fetchOne(
            "SELECT COUNT(*) FROM user WHERE id = :id",
            ["id" => TestUsers::USER2_ID],
        );

        if (!$existingUser2) {
            $user2 = User::create(
                new Uuid(TestUsers::USER2_ID),
                new FirstnameValue(TestUsers::USER2_FIRSTNAME),
                new LastnameValue(TestUsers::USER2_LASTNAME),
                new UsernameValue(TestUsers::USER2_USERNAME),
                new EmailValue(TestUsers::USER2_EMAIL),
                new PasswordValue(TestUsers::USER2_PASSWORD),
            );
            $this->entityManager->persist($user2);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /** @AfterScenario @user */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Limpiar seguidores/seguidos relacionados con usuarios de prueba
            $connection->executeStatement(
                "DELETE FROM follower WHERE follower_id IN (:id1, :id2) OR followed_id IN (:id3, :id4)",
                [
                    "id1" => TestUsers::USER1_ID,
                    "id2" => TestUsers::USER2_ID,
                    "id3" => TestUsers::USER1_ID,
                    "id4" => TestUsers::USER2_ID,
                ],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar usuarios creados dinámicamente en los tests (no los usuarios base)
            $connection->executeStatement(
                "DELETE FROM user WHERE id NOT IN (:id1, :id2)",
                [
                    "id1" => TestUsers::USER1_ID,
                    "id2" => TestUsers::USER2_ID,
                ],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // Limpiar el entity manager
        $this->entityManager->clear();
    }
}
