<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Auth;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use App\Tests\Behat\Shared\Fixtures\TestUsers;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class AuthTestContext implements Context
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    /** @BeforeScenario @auth */
    public function createTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // Verificar si el usuario ya existe antes de intentar crearlo
        $userExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM user WHERE id = :id",
            ["id" => TestUsers::USER1_ID],
        );

        if ($userExists) {
            // El usuario ya existe, no hacer nada
            return;
        }

        // Limpiar cualquier usuario existente con el mismo username o email ANTES de crear
        $this->cleanupTestUser();

        // Crear usuario de prueba para login
        // IMPORTANTE: PasswordValue hashea automáticamente, pasar texto plano
        $user = User::create(
            new Uuid(TestUsers::USER1_ID),
            new FirstnameValue(TestUsers::USER1_FIRSTNAME),
            new LastnameValue(TestUsers::USER1_LASTNAME),
            new UsernameValue(TestUsers::USER1_USERNAME),
            new EmailValue(TestUsers::USER1_EMAIL),
            new PasswordValue(TestUsers::USER1_PASSWORD), // NO hashear aquí
        );

        $this->userRepository->save($user);
    }

    /** @AfterScenario @auth */
    public function cleanupTestData(): void
    {
        $this->cleanupTestUser();
    }

    private function cleanupTestUser(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Limpiar con SQL nativo para evitar problemas de DQL y cacheo
            // Limpiar por email (más confiable que por ID cuando hay duplicados)
            $connection->executeStatement(
                "DELETE FROM user WHERE email = :email",
                ["email" => TestUsers::USER1_EMAIL],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar por username también por si acaso
            $connection->executeStatement(
                "DELETE FROM user WHERE username = :username",
                ["username" => TestUsers::USER1_USERNAME],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar por ID también
            $connection->executeStatement("DELETE FROM user WHERE id = :id", [
                "id" => TestUsers::USER1_ID,
            ]);
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // Limpiar cualquier cambio pendiente en el EntityManager
        $this->entityManager->clear();
    }
}
