<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\Post;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\UserRepository;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class PostTestContext implements Context
{
    // IDs de usuarios de la migración Version20251119000001
    private const USER1_ID = '550e8400-e29b-41d4-a716-446655440001';

    private const TEST_POST_ID = "550e8400-e29b-41d4-a716-446655440010";

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /** @BeforeScenario @post */
    public function createTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // Los notification_types ya existen en la base de datos creados por la migración Version20251116054200
        // - post_liked (ID: 850e8400-e29b-41d4-a716-446655440001)
        // - post_commented (ID: 850e8400-e29b-41d4-a716-446655440002)
        // NO necesitamos crearlos aquí.

        // Los usuarios globales ya existen, solo obtenerlos
        $user = $this->userRepository->findById(
            new Uuid(self::USER1_ID),
        );

        // Verificar si el post ya existe
        $postExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM post WHERE id = :id",
            ["id" => self::TEST_POST_ID],
        );

        if ($postExists) {
            // El post ya existe, no hacer nada
            return;
        }

        // Crear un post de prueba para el usuario compartido
        $post = new Post(
            new Uuid(self::TEST_POST_ID),
            new BodyValue("This is a test post about gaming and esports!"),
            $user,
            null, // sharedPostId
        );
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    /** @AfterScenario @post */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Limpiar notificaciones de todos los posts del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM notification WHERE post_id IN (SELECT id FROM post WHERE user_id = :userId)",
                ["userId" => self::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar comentarios de todos los posts del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM post_comment WHERE post_id IN (SELECT id FROM post WHERE user_id = :userId)",
                ["userId" => self::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar likes/dislikes de todos los posts del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM post_like WHERE post_id IN (SELECT id FROM post WHERE user_id = :userId)",
                ["userId" => self::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar todos los posts del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM post WHERE user_id = :userId",
                ["userId" => self::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // NO limpiar notification_types - son datos de migración y NO deben eliminarse
        // Los notification_types (post_liked, post_commented, etc.) vienen de Version20251116054200

        $this->entityManager->clear();
    }
}
