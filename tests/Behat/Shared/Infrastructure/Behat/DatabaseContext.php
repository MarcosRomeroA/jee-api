<?php

declare(strict_types=1);

namespace App\Tests\Behat\Shared\Infrastructure\Behat;

use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Behat\Shared\Fixtures\TestUsers;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Process;

final class DatabaseContext implements Context
{
    private static bool $initialized = false;
    private array $createdUserIds = [];

    private const USER1_ID = "550e8400-e29b-41d4-a716-446655440001";
    private const USER2_ID = "550e8400-e29b-41d4-a716-446655440002";
    private const USER3_ID = "550e8400-e29b-41d4-a716-446655440003";

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /** @BeforeScenario */
    public function ensureDefaultUsers(): void
    {
        $connection = $this->entityManager->getConnection();

        try {
            // Restaurar emails originales de los usuarios de prueba antes de cada escenario
            // NO actualizar la contraseña para evitar cambiar el hash
            $connection->executeStatement(
                "UPDATE user SET email = :email1, username = :username1, firstname = :firstname1, lastname = :lastname1 WHERE id = :id1",
                [
                    "id1" => self::USER1_ID,
                    "email1" => "test@example.com",
                    "username1" => "testuser",
                    "firstname1" => "Test",
                    "lastname1" => "User",
                ]
            );
            $connection->executeStatement(
                "UPDATE user SET email = :email2, username = :username2, firstname = :firstname2, lastname = :lastname2 WHERE id = :id2",
                [
                    "id2" => self::USER2_ID,
                    "email2" => "jane@example.com",
                    "username2" => "janesmith",
                    "firstname2" => "Jane",
                    "lastname2" => "Smith",
                ]
            );
            $connection->executeStatement(
                "UPDATE user SET email = :email3, username = :username3, firstname = :firstname3, lastname = :lastname3 WHERE id = :id3",
                [
                    "id3" => self::USER3_ID,
                    "email3" => "bob@example.com",
                    "username3" => "bobtest",
                    "firstname3" => "Bob",
                    "lastname3" => "Test",
                ]
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        $this->entityManager->clear();
    }

    /** @BeforeSuite */
    public static function setupDatabase(): void
    {
        if (self::$initialized) {
            return;
        }

        echo "\n🔧 Inicializando base de datos para tests...\n";

        // Crear la base de datos si no existe y ejecutar migraciones
        $createResult = self::executeCommand([
            "php",
            "bin/console",
            "doctrine:database:create",
            "--if-not-exists",
            "--env=test",
        ]);

        if ($createResult["success"]) {
            echo "✓ Base de datos verificada/creada\n";
        }

        // Ejecutar migraciones
        $migrateResult = self::executeCommand([
            "php",
            "bin/console",
            "doctrine:migrations:migrate",
            "--no-interaction",
            "--env=test",
        ]);

        if ($migrateResult["success"]) {
            echo "✓ Migraciones ejecutadas\n";
        } else {
            echo "✗ Error al ejecutar migraciones\n";
            echo $migrateResult["error"];
        }

        // Crear usuarios globales para todos los tests
        self::createGlobalTestUsers();

        echo "✅ Base de datos inicializada correctamente\n\n";

        self::$initialized = true;
    }

    /**
     * Crea los 3 usuarios globales que se usarán en TODOS los tests.
     * Estos usuarios se crean UNA SOLA VEZ al inicio de la suite y persisten durante todos los tests.
     * Los passwords se hashean aquí y nunca se modifican, garantizando autenticación consistente.
     */
    private static function createGlobalTestUsers(): void
    {
        // Obtener la conexión directamente desde el EntityManager bootstrap
        $kernel = new \App\Kernel($_ENV['APP_ENV'] ?? 'test', (bool) ($_ENV['APP_DEBUG'] ?? false));
        $kernel->boot();
        $container = $kernel->getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $connection = $entityManager->getConnection();

        echo "👥 Creando usuarios globales para tests...\n";

        // Calcular hashes UNA SOLA VEZ para passwords conocidos
        $user1Hash = password_hash(TestUsers::USER1_PASSWORD, PASSWORD_BCRYPT);
        $user2Hash = password_hash(TestUsers::USER2_PASSWORD, PASSWORD_BCRYPT);
        $user3Hash = password_hash(TestUsers::USER3_PASSWORD, PASSWORD_BCRYPT);

        try {
            // Verificar si los usuarios ya existen
            $user1Exists = $connection->fetchOne(
                "SELECT COUNT(*) FROM user WHERE id = :id",
                ["id" => TestUsers::USER1_ID]
            );

            if ($user1Exists > 0) {
                // Los usuarios ya existen, actualizar sus passwords para asegurar que sean correctos
                echo "  🔄 Usuarios globales ya existen, actualizando passwords...\n";

                $connection->executeStatement(
                    "UPDATE user SET password = :password WHERE id = :id",
                    ["id" => TestUsers::USER1_ID, "password" => $user1Hash]
                );
                $connection->executeStatement(
                    "UPDATE user SET password = :password WHERE id = :id",
                    ["id" => TestUsers::USER2_ID, "password" => $user2Hash]
                );
                $connection->executeStatement(
                    "UPDATE user SET password = :password WHERE id = :id",
                    ["id" => TestUsers::USER3_ID, "password" => $user3Hash]
                );

                echo "  ✓ Passwords actualizados correctamente\n";

                // Asegurar que tengan email confirmado
                $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
                $expiresAt = (new \DateTimeImmutable())->modify('+24 hours')->format('Y-m-d H:i:s');

                foreach ([TestUsers::USER1_ID, TestUsers::USER2_ID, TestUsers::USER3_ID] as $userId) {
                    // Generar UUID válido
                    $uuid = \sprintf(
                        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                        \random_int(0, 0xffff),
                        \random_int(0, 0xffff),
                        \random_int(0, 0xffff),
                        \random_int(0, 0x0fff) | 0x4000,
                        \random_int(0, 0x3fff) | 0x8000,
                        \random_int(0, 0xffff),
                        \random_int(0, 0xffff),
                        \random_int(0, 0xffff)
                    );

                    $connection->executeStatement(
                        "INSERT INTO email_confirmation (id, user_id, token, created_at, expires_at, confirmed_at)
                         VALUES (:id, :user_id, :token, :created_at, :expires_at, :confirmed_at)
                         ON DUPLICATE KEY UPDATE confirmed_at = :confirmed_at",
                        [
                            "id" => $uuid,
                            "user_id" => $userId,
                            "token" => \bin2hex(\random_bytes(32)),
                            "created_at" => $now,
                            "expires_at" => $expiresAt,
                            "confirmed_at" => $now,
                        ]
                    );
                }

                echo "  ✓ Email confirmations verificadas\n";

                $kernel->shutdown();
                return;
            }

            // Crear USER1 (test@example.com)
            $connection->executeStatement(
                "INSERT INTO user (id, firstname, lastname, username, email, password, profile_image, created_at)
                 VALUES (:id, :firstname, :lastname, :username, :email, :password, '', NOW())",
                [
                    "id" => TestUsers::USER1_ID,
                    "firstname" => TestUsers::USER1_FIRSTNAME,
                    "lastname" => TestUsers::USER1_LASTNAME,
                    "username" => TestUsers::USER1_USERNAME,
                    "email" => TestUsers::USER1_EMAIL,
                    "password" => $user1Hash,
                ]
            );
            echo "  ✓ USER1 creado: {" . TestUsers::USER1_EMAIL . "}\n";

            // Crear USER2 (jane@example.com)
            $connection->executeStatement(
                "INSERT INTO user (id, firstname, lastname, username, email, password, profile_image, created_at)
                 VALUES (:id, :firstname, :lastname, :username, :email, :password, '', NOW())",
                [
                    "id" => TestUsers::USER2_ID,
                    "firstname" => TestUsers::USER2_FIRSTNAME,
                    "lastname" => TestUsers::USER2_LASTNAME,
                    "username" => TestUsers::USER2_USERNAME,
                    "email" => TestUsers::USER2_EMAIL,
                    "password" => $user2Hash,
                ]
            );
            echo "  ✓ USER2 creado: {" . TestUsers::USER2_EMAIL . "}\n";

            // Crear USER3 (bob@example.com)
            $connection->executeStatement(
                "INSERT INTO user (id, firstname, lastname, username, email, password, profile_image, created_at)
                 VALUES (:id, :firstname, :lastname, :username, :email, :password, '', NOW())",
                [
                    "id" => TestUsers::USER3_ID,
                    "firstname" => TestUsers::USER3_FIRSTNAME,
                    "lastname" => TestUsers::USER3_LASTNAME,
                    "username" => TestUsers::USER3_USERNAME,
                    "email" => TestUsers::USER3_EMAIL,
                    "password" => $user3Hash,
                ]
            );
            echo "  ✓ USER3 creado: {" . TestUsers::USER3_EMAIL . "}\n";

            // Crear confirmaciones de email para los 3 usuarios (ya confirmados)
            $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            $expiresAt = (new \DateTimeImmutable())->modify('+24 hours')->format('Y-m-d H:i:s');

            foreach ([TestUsers::USER1_ID, TestUsers::USER2_ID, TestUsers::USER3_ID] as $userId) {
                // Generar UUID válido
                $uuid = \sprintf(
                    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    \random_int(0, 0xffff),
                    \random_int(0, 0xffff),
                    \random_int(0, 0xffff),
                    \random_int(0, 0x0fff) | 0x4000,
                    \random_int(0, 0x3fff) | 0x8000,
                    \random_int(0, 0xffff),
                    \random_int(0, 0xffff),
                    \random_int(0, 0xffff)
                );

                $connection->executeStatement(
                    "INSERT INTO email_confirmation (id, user_id, token, created_at, expires_at, confirmed_at)
                     VALUES (:id, :user_id, :token, :created_at, :expires_at, :confirmed_at)
                     ON DUPLICATE KEY UPDATE confirmed_at = :confirmed_at",
                    [
                        "id" => $uuid,
                        "user_id" => $userId,
                        "token" => \bin2hex(\random_bytes(32)),
                        "created_at" => $now,
                        "expires_at" => $expiresAt,
                        "confirmed_at" => $now,
                    ]
                );
            }

            echo "  ✓ Email confirmations creadas para usuarios globales\n";

        } catch (\Exception $e) {
            echo "  ✗ Error creando usuarios globales: " . $e->getMessage() . "\n";
        }

        $kernel->shutdown();
    }

    /**
     * @Given the following users exist:
     */
    public function theFollowingUsersExist(TableNode $table): void
    {
        $connection = $this->entityManager->getConnection();

        foreach ($table->getHash() as $row) {
            // Verificar si el usuario ya existe y obtener su email
            $existingEmail = $connection->fetchOne(
                "SELECT email FROM user WHERE id = :id",
                ["id" => $row['id']]
            );

            // Hashear la contraseña
            $hashedPassword = password_hash($row['password'], PASSWORD_BCRYPT);

            if ($existingEmail) {
                // Usuario existe - siempre limpiar relaciones de seguimiento y actualizar
                try {
                    // Limpiar relaciones de seguimiento antes de actualizar
                    $connection->executeStatement(
                        "DELETE FROM user_follow WHERE follower_id = :id OR followed_id = :id",
                        ["id" => $row['id']]
                    );

                    // Actualizar el usuario con los nuevos datos si el email es diferente
                    if ($existingEmail !== $row['email']) {
                        $connection->executeStatement(
                            "UPDATE user SET firstname = :firstname, lastname = :lastname, username = :username, email = :email, password = :password WHERE id = :id",
                            [
                                "id" => $row['id'],
                                "firstname" => $row['firstname'],
                                "lastname" => $row['lastname'],
                                "username" => $row['username'],
                                "email" => $row['email'],
                                "password" => $hashedPassword,
                            ]
                        );
                    }
                } catch (\Exception $e) {
                    // Ignorar errores al actualizar
                }
            } else {
                // Usuario no existe - crearlo
                try {
                    $connection->executeStatement(
                        "INSERT INTO user (id, firstname, lastname, username, email, password, profile_image, created_at)
                         VALUES (:id, :firstname, :lastname, :username, :email, :password, '', NOW())",
                        [
                            "id" => $row['id'],
                            "firstname" => $row['firstname'],
                            "lastname" => $row['lastname'],
                            "username" => $row['username'],
                            "email" => $row['email'],
                            "password" => $hashedPassword,
                        ]
                    );
                } catch (\Exception $e) {
                    // Ignorar si hay error
                }
            }
        }
    }

    /**
     * @Given the following email confirmations exist:
     */
    public function theFollowingEmailConfirmationsExist(TableNode $table): void
    {
        $connection = $this->entityManager->getConnection();

        foreach ($table->getHash() as $row) {
            // Generar UUID para el email confirmation
            $uuid = \sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                \random_int(0, 0xffff),
                \random_int(0, 0xffff),
                \random_int(0, 0xffff),
                \random_int(0, 0x0fff) | 0x4000,
                \random_int(0, 0x3fff) | 0x8000,
                \random_int(0, 0xffff),
                \random_int(0, 0xffff),
                \random_int(0, 0xffff)
            );

            $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            $expiresAt = isset($row['expires_at']) && $row['expires_at'] !== 'null'
                ? $row['expires_at']
                : (new \DateTimeImmutable())->modify('+24 hours')->format('Y-m-d H:i:s');

            $confirmedAt = isset($row['confirmed_at']) && $row['confirmed_at'] !== 'null'
                ? $row['confirmed_at']
                : null;

            try {
                // Primero eliminar cualquier confirmación existente para este usuario
                $connection->executeStatement(
                    "DELETE FROM email_confirmation WHERE user_id = :user_id",
                    ["user_id" => $row['user_id']]
                );

                // Insertar la nueva confirmación
                $connection->executeStatement(
                    "INSERT INTO email_confirmation (id, user_id, token, created_at, expires_at, confirmed_at)
                     VALUES (:id, :user_id, :token, :created_at, :expires_at, :confirmed_at)",
                    [
                        "id" => $uuid,
                        "user_id" => $row['user_id'],
                        "token" => $row['token'],
                        "created_at" => $now,
                        "expires_at" => $expiresAt,
                        "confirmed_at" => $confirmedAt,
                    ]
                );
            } catch (\Exception $e) {
                // Ignorar si hay error
            }
        }
    }

    /** @AfterScenario */
    public function cleanupDynamicUsers(): void
    {
        // NO limpiar usuarios que fueron creados dinámicamente si usan IDs de TestUsers
        // Estos usuarios son compartidos entre tests y no deben eliminarse
        // El cleanup lo harán sus respectivos contextos (UserTestContext, etc.)

        $this->createdUserIds = [];
        $this->entityManager->clear();
    }

    /**
     * @param array<string> $command
     * @return array{success: bool, error: string}
     */
    private static function executeCommand(array $command): array
    {
        $process = new Process($command);
        $process->setTimeout(300); // 5 minutos timeout
        $process->run();

        return [
            "success" => $process->isSuccessful(),
            "error" => $process->getErrorOutput(),
        ];
    }
}
