<?php

declare(strict_types=1);

namespace App\Tests\Behat\Shared\Infrastructure\Behat;

use App\Contexts\Web\User\Domain\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Process;

final class DatabaseContext implements Context
{
    private static bool $initialized = false;
    private array $createdUserIds = [];
    private array $createdEmailConfirmationIds = [];
    private array $createdAdminIds = [];
    private array $createdPostIds = [];
    private array $createdHashtagIds = [];
    private array $createdTeamIds = [];

    private const USER1_ID = "550e8400-e29b-41d4-a716-446655440001";
    private const USER2_ID = "550e8400-e29b-41d4-a716-446655440002";
    private const USER3_ID = "550e8400-e29b-41d4-a716-446655440003";

    // ID del admin por defecto creado por migración
    private const DEFAULT_ADMIN_ID = "a50e8400-e29b-41d4-a716-446655440000";

    // IDs de usuarios de migración que NO deben modificarse
    private const MIGRATION_USER_IDS = [
        self::USER1_ID,
        self::USER2_ID,
        self::USER3_ID,
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /** @BeforeScenario */
    public function ensureDefaultUsers(): void
    {
        // Los usuarios estáticos (tester1, tester2, tester3) ya existen en la base de datos
        // Son creados por la migración Version20251119000001 y NO deben ser modificados
        // Solo limpiamos el entity manager
        $this->entityManager->clear();
    }

    /** @BeforeSuite */
    public static function setupDatabase(): void
    {
        if (self::$initialized) {
            return;
        }

        echo "\n🔧 Inicializando base de datos para tests...\n";

        // SIEMPRE eliminar y recrear la base de datos para tener un estado limpio
        echo "🗑️  Eliminando base de datos existente...\n";
        $dropResult = self::executeCommand([
            "php",
            "bin/console",
            "doctrine:database:drop",
            "--force",
            "--env=test",
        ]);

        if ($dropResult["success"]) {
            echo "✓ Base de datos eliminada\n";
        }

        // Crear la base de datos
        $createResult = self::executeCommand([
            "php",
            "bin/console",
            "doctrine:database:create",
            "--env=test",
        ]);

        if ($createResult["success"]) {
            echo "✓ Base de datos creada\n";
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

        echo "✅ Base de datos inicializada correctamente\n\n";

        self::$initialized = true;
    }

    /**
     * @Given the following users exist:
     */
    public function theFollowingUsersExist(TableNode $table): void
    {
        $connection = $this->entityManager->getConnection();

        foreach ($table->getHash() as $row) {
            $userId = $row['id'];

            // Verificar si es un usuario de migración (NO debe modificarse)
            $isMigrationUser = in_array($userId, self::MIGRATION_USER_IDS);

            // Verificar si el usuario ya existe
            $existingEmail = $connection->fetchOne(
                "SELECT email FROM user WHERE id = :id",
                ["id" => $userId]
            );

            // Si es un usuario de migración, NO modificarlo
            if ($isMigrationUser && $existingEmail) {
                // Usuario de migración ya existe - NO modificar, solo continuar
                continue;
            }

            // Hashear la contraseña
            $hashedPassword = password_hash($row['password'], PASSWORD_BCRYPT);

            if ($existingEmail && !$isMigrationUser) {
                // Usuario NO es de migración pero ya existe - esto no debería pasar
                // porque cada test debe limpiar sus datos
                throw new \RuntimeException(
                    "Usuario con ID {$userId} ya existe pero NO es un usuario de migración. " .
                    "El contexto anterior no limpió correctamente sus datos."
                );
            } else {
                // Usuario no existe - crearlo (verificado por defecto para facilitar tests)
                $connection->executeStatement(
                    "INSERT INTO user (id, firstname, lastname, username, email, password, profile_image, description, created_at, verified_at)
                     VALUES (:id, :firstname, :lastname, :username, :email, :password, '', '', NOW(), NOW())",
                    [
                        "id" => $userId,
                        "firstname" => $row['firstname'],
                        "lastname" => $row['lastname'],
                        "username" => $row['username'],
                        "email" => $row['email'],
                        "password" => $hashedPassword,
                    ]
                );

                // Registrar que este contexto creó este usuario
                $this->createdUserIds[] = $userId;
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
            $userId = $row['user_id'];

            // Verificar si es un usuario de migración (NO debe modificarse)
            $isMigrationUser = in_array($userId, self::MIGRATION_USER_IDS);

            if ($isMigrationUser) {
                throw new \RuntimeException(
                    "No se puede crear email_confirmation para usuario de migración {$userId}. " .
                    "Los usuarios de migración (tester1, tester2, tester3) ya están verificados y NO deben modificarse."
                );
            }

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

            $createdAt = isset($row['created_at']) && $row['created_at'] !== 'null'
                ? $row['created_at']
                : $now;

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
                    ["user_id" => $userId]
                );

                // Actualizar el verified_at del usuario según confirmed_at
                // Solo para usuarios NO de migración
                if ($confirmedAt !== null) {
                    // Si confirmed_at no es null, marcar al usuario como verificado
                    $connection->executeStatement(
                        "UPDATE user SET verified_at = :verified_at WHERE id = :user_id",
                        [
                            "verified_at" => $confirmedAt,
                            "user_id" => $userId
                        ]
                    );
                } else {
                    // Si confirmed_at es null, resetear verified_at (usuario no verificado)
                    $connection->executeStatement(
                        "UPDATE user SET verified_at = NULL WHERE id = :user_id",
                        ["user_id" => $userId]
                    );
                }

                // Insertar la nueva confirmación
                $connection->executeStatement(
                    "INSERT INTO email_confirmation (id, user_id, token, created_at, expires_at, confirmed_at)
                     VALUES (:id, :user_id, :token, :created_at, :expires_at, :confirmed_at)",
                    [
                        "id" => $uuid,
                        "user_id" => $userId,
                        "token" => $row['token'],
                        "created_at" => $createdAt,
                        "expires_at" => $expiresAt,
                        "confirmed_at" => $confirmedAt,
                    ]
                );

                // Registrar que este contexto creó este email_confirmation
                $this->createdEmailConfirmationIds[] = $uuid;
            } catch (\Exception $e) {
                // Ignorar si hay error
            }
        }
    }

    /**
     * @Given the following admins exist:
     */
    public function theFollowingAdminsExist(TableNode $table): void
    {
        $connection = $this->entityManager->getConnection();

        foreach ($table->getHash() as $row) {
            $adminId = $row['id'];

            // Verificar si es el admin por defecto (NO debe modificarse)
            if ($adminId === self::DEFAULT_ADMIN_ID) {
                // Admin por defecto ya existe en la migración - NO modificar
                continue;
            }

            // Verificar si el admin ya existe
            $existingAdmin = $connection->fetchOne(
                "SELECT user FROM admin WHERE id = :id",
                ["id" => $adminId]
            );

            // Hashear la contraseña
            $hashedPassword = password_hash($row['password'], PASSWORD_BCRYPT);

            if ($existingAdmin) {
                // Admin ya existe - actualizarlo y resetear deleted_at
                $connection->executeStatement(
                    "UPDATE admin SET name = :name, user = :user, password = :password, updated_at = NOW(), deleted_at = NULL
                     WHERE id = :id",
                    [
                        "id" => $adminId,
                        "name" => $row['name'],
                        "user" => $row['user'],
                        "password" => $hashedPassword,
                    ]
                );
            } else {
                // Admin no existe - crearlo
                $role = $row['role'] ?? 'admin';
                $connection->executeStatement(
                    "INSERT INTO admin (id, name, user, password, role, created_at)
                     VALUES (:id, :name, :user, :password, :role, NOW())",
                    [
                        "id" => $adminId,
                        "name" => $row['name'],
                        "user" => $row['user'],
                        "password" => $hashedPassword,
                        "role" => $role,
                    ]
                );
            }

            // Siempre registrar que este contexto maneja este admin (para limpieza)
            if (!in_array($adminId, $this->createdAdminIds)) {
                $this->createdAdminIds[] = $adminId;
            }
        }
    }

    /**
     * @Given the following posts exist:
     */
    public function theFollowingPostsExist(TableNode $table): void
    {
        $connection = $this->entityManager->getConnection();

        foreach ($table->getHash() as $row) {
            $postId = $row['id'];
            $userId = $row['user_id'];
            $body = $row['body'];
            $disabled = isset($row['disabled']) && $row['disabled'] === 'true';

            // moderation_reason debe ser NULL si está vacío o no definido
            $moderationReason = null;
            if (isset($row['moderation_reason']) && $row['moderation_reason'] !== '') {
                $moderationReason = $row['moderation_reason'];
            }

            $connection->executeStatement(
                "INSERT INTO post (id, user_id, body, disabled, moderation_reason, created_at, updated_at)
                 VALUES (:id, :user_id, :body, :disabled, :moderation_reason, NOW(), NOW())",
                [
                    "id" => $postId,
                    "user_id" => $userId,
                    "body" => $body,
                    "disabled" => $disabled ? 1 : 0,
                    "moderation_reason" => $moderationReason,
                ]
            );

            $this->createdPostIds[] = $postId;
        }
    }

    /**
     * @Given the following teams exist:
     */
    public function theFollowingTeamsExist(TableNode $table): void
    {
        $connection = $this->entityManager->getConnection();

        foreach ($table->getHash() as $row) {
            $teamId = $row['id'];
            $name = $row['name'];
            $creatorId = $row['creator_id'];
            $description = $row['description'] ?? '';
            $disabled = isset($row['disabled']) && $row['disabled'] === 'true';

            $moderationReason = null;
            if (isset($row['moderation_reason']) && $row['moderation_reason'] !== '') {
                $moderationReason = $row['moderation_reason'];
            }

            $disabledAt = $disabled ? date('Y-m-d H:i:s') : null;

            // Insert team
            $connection->executeStatement(
                "INSERT INTO team (id, name, description, image, background_image, is_disabled, moderation_reason, disabled_at, created_at, updated_at)
                 VALUES (:id, :name, :description, '', '', :is_disabled, :moderation_reason, :disabled_at, NOW(), NOW())",
                [
                    "id" => $teamId,
                    "name" => $name,
                    "description" => $description,
                    "is_disabled" => $disabled ? 1 : 0,
                    "moderation_reason" => $moderationReason,
                    "disabled_at" => $disabledAt,
                ]
            );

            // Add creator as team_user with creator and leader flags
            $teamUserId = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0x0fff) | 0x4000,
                random_int(0, 0x3fff) | 0x8000,
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0xffff)
            );

            $connection->executeStatement(
                "INSERT INTO team_user (id, team_id, user_id, is_creator, is_leader, joined_at)
                 VALUES (:id, :team_id, :user_id, 1, 1, NOW())",
                [
                    "id" => $teamUserId,
                    "team_id" => $teamId,
                    "user_id" => $creatorId,
                ]
            );

            $this->createdTeamIds[] = $teamId;
        }
    }

    /**
     * @Given the following hashtags exist:
     */
    public function theFollowingHashtagsExist(TableNode $table): void
    {
        $connection = $this->entityManager->getConnection();

        foreach ($table->getHash() as $row) {
            $hashtagId = $row['id'];
            $tag = strtolower(ltrim($row['tag'], '#'));
            $count = isset($row['count']) ? (int) $row['count'] : 0;
            $disabled = isset($row['disabled']) && $row['disabled'] === 'true';

            $deletedAt = $disabled ? date('Y-m-d H:i:s') : null;

            $connection->executeStatement(
                "INSERT INTO hashtag (id, tag, count, created_at, updated_at, deleted_at)
                 VALUES (:id, :tag, :count, NOW(), NOW(), :deleted_at)",
                [
                    "id" => $hashtagId,
                    "tag" => $tag,
                    "count" => $count,
                    "deleted_at" => $deletedAt,
                ]
            );

            $this->createdHashtagIds[] = $hashtagId;
        }
    }

    /** @BeforeScenario */
    public function cleanupAllAdmins(): void
    {
        // Limpiar TODOS los admins EXCEPTO el admin por defecto de la migración
        $connection = $this->entityManager->getConnection();
        try {
            $connection->executeStatement(
                "DELETE FROM admin WHERE id != :default_admin_id",
                ["default_admin_id" => self::DEFAULT_ADMIN_ID]
            );
        } catch (\Exception $e) {
            // Ignorar errores
        }
        $this->createdAdminIds = [];
    }

    /** @AfterScenario */
    public function cleanupDynamicUsers(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // Limpiar posts creados por ESTE contexto
        if (!empty($this->createdPostIds)) {
            try {
                $placeholders = implode(',', array_fill(0, count($this->createdPostIds), '?'));

                // Limpiar likes de posts
                $connection->executeStatement(
                    "DELETE FROM `like` WHERE post_id IN ({$placeholders})",
                    $this->createdPostIds
                );

                // Limpiar comentarios de posts
                $connection->executeStatement(
                    "DELETE FROM comment WHERE post_id IN ({$placeholders})",
                    $this->createdPostIds
                );

                // Limpiar post_hashtag
                $connection->executeStatement(
                    "DELETE FROM post_hashtag WHERE post_id IN ({$placeholders})",
                    $this->createdPostIds
                );

                // Limpiar post_resource
                $connection->executeStatement(
                    "DELETE FROM post_resource WHERE post_id IN ({$placeholders})",
                    $this->createdPostIds
                );

                // Finalmente eliminar posts
                $connection->executeStatement(
                    "DELETE FROM post WHERE id IN ({$placeholders})",
                    $this->createdPostIds
                );
            } catch (\Exception $e) {
                // Ignorar errores
            }
        }

        // Limpiar email_confirmations creados por ESTE contexto
        if (!empty($this->createdEmailConfirmationIds)) {
            try {
                $placeholders = implode(',', array_fill(0, count($this->createdEmailConfirmationIds), '?'));
                $connection->executeStatement(
                    "DELETE FROM email_confirmation WHERE id IN ({$placeholders})",
                    $this->createdEmailConfirmationIds
                );
            } catch (\Exception $e) {
                // Ignorar errores
            }
        }

        // Limpiar teams creados por ESTE contexto (ANTES de usuarios por FK)
        if (!empty($this->createdTeamIds)) {
            try {
                $placeholders = implode(',', array_fill(0, count($this->createdTeamIds), '?'));

                // Limpiar team_user
                $connection->executeStatement(
                    "DELETE FROM team_user WHERE team_id IN ({$placeholders})",
                    $this->createdTeamIds
                );

                // Limpiar team_game
                $connection->executeStatement(
                    "DELETE FROM team_game WHERE team_id IN ({$placeholders})",
                    $this->createdTeamIds
                );

                // Limpiar team_request
                $connection->executeStatement(
                    "DELETE FROM team_request WHERE team_id IN ({$placeholders})",
                    $this->createdTeamIds
                );

                // Finalmente eliminar teams
                $connection->executeStatement(
                    "DELETE FROM team WHERE id IN ({$placeholders})",
                    $this->createdTeamIds
                );
            } catch (\Exception $e) {
                // Ignorar errores
            }
        }

        // Limpiar usuarios creados por ESTE contexto (NO los de migración)
        if (!empty($this->createdUserIds)) {
            try {
                // Primero limpiar relaciones
                $placeholders = implode(',', array_fill(0, count($this->createdUserIds), '?'));

                // Limpiar team_user por user_id
                $connection->executeStatement(
                    "DELETE FROM team_user WHERE user_id IN ({$placeholders})",
                    $this->createdUserIds
                );

                // Limpiar user_follow
                $connection->executeStatement(
                    "DELETE FROM user_follow WHERE follower_id IN ({$placeholders}) OR followed_id IN ({$placeholders})",
                    array_merge($this->createdUserIds, $this->createdUserIds)
                );

                // Limpiar email_confirmation
                $connection->executeStatement(
                    "DELETE FROM email_confirmation WHERE user_id IN ({$placeholders})",
                    $this->createdUserIds
                );

                // Finalmente eliminar usuarios
                $connection->executeStatement(
                    "DELETE FROM user WHERE id IN ({$placeholders})",
                    $this->createdUserIds
                );
            } catch (\Exception $e) {
                // Ignorar errores
            }
        }

        // Limpiar admins creados por ESTE contexto (NUNCA el admin por defecto)
        if (!empty($this->createdAdminIds)) {
            try {
                // Filtrar el admin por defecto de la lista de admins a eliminar
                $adminsToDelete = array_filter(
                    $this->createdAdminIds,
                    fn ($id) => $id !== self::DEFAULT_ADMIN_ID
                );

                if (!empty($adminsToDelete)) {
                    $placeholders = implode(',', array_fill(0, count($adminsToDelete), '?'));
                    $connection->executeStatement(
                        "DELETE FROM admin WHERE id IN ({$placeholders})",
                        $adminsToDelete
                    );
                }
            } catch (\Exception $e) {
                // Ignorar errores
            }
        }

        // Limpiar hashtags creados por ESTE contexto
        if (!empty($this->createdHashtagIds)) {
            try {
                $placeholders = implode(',', array_fill(0, count($this->createdHashtagIds), '?'));
                $connection->executeStatement(
                    "DELETE FROM hashtag WHERE id IN ({$placeholders})",
                    $this->createdHashtagIds
                );
            } catch (\Exception $e) {
                // Ignorar errores
            }
        }

        // Resetear arrays de seguimiento
        $this->createdUserIds = [];
        $this->createdEmailConfirmationIds = [];
        $this->createdAdminIds = [];
        $this->createdPostIds = [];
        $this->createdHashtagIds = [];
        $this->createdTeamIds = [];
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
