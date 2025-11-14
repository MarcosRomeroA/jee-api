<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Post;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
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

final class PostTestContext implements Context
{
    private const TEST_POST_ID = "550e8400-e29b-41d4-a716-446655440010";

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {}

    /** @BeforeScenario @post */
    public function createTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // Crear notification_type para POST_LIKED si no existe
        $notificationTypeLikedExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM notification_type WHERE name = :name",
            ["name" => "POST_LIKED"],
        );

        if ($notificationTypeLikedExists == 0) {
            $connection->executeStatement(
                "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
                [
                    "id" => "850e8400-e29b-41d4-a716-446655440001",
                    "name" => "POST_LIKED",
                ],
            );
        }

        // Crear notification_type para POST_COMMENTED si no existe
        $notificationTypeCommentedExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM notification_type WHERE name = :name",
            ["name" => "POST_COMMENTED"],
        );

        if ($notificationTypeCommentedExists == 0) {
            $connection->executeStatement(
                "INSERT INTO notification_type (id, name) VALUES (:id, :name)",
                [
                    "id" => "850e8400-e29b-41d4-a716-446655440002",
                    "name" => "POST_COMMENTED",
                ],
            );
        }

        // Obtener o crear el usuario compartido
        try {
            $user = $this->userRepository->findById(
                new Uuid(TestUsers::USER1_ID),
            );
        } catch (\Exception $e) {
            $user = User::create(
                new Uuid(TestUsers::USER1_ID),
                new FirstnameValue(TestUsers::USER1_FIRSTNAME),
                new LastnameValue(TestUsers::USER1_LASTNAME),
                new UsernameValue(TestUsers::USER1_USERNAME),
                new EmailValue(TestUsers::USER1_EMAIL),
                new PasswordValue(TestUsers::USER1_PASSWORD),
            );
            $this->userRepository->save($user);
        }

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
                ["userId" => TestUsers::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar comentarios de todos los posts del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM post_comment WHERE post_id IN (SELECT id FROM post WHERE user_id = :userId)",
                ["userId" => TestUsers::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar likes/dislikes de todos los posts del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM post_like WHERE post_id IN (SELECT id FROM post WHERE user_id = :userId)",
                ["userId" => TestUsers::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar todos los posts del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM post WHERE user_id = :userId",
                ["userId" => TestUsers::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // NO eliminar el usuario - es compartido entre contextos
        // Limpiar el entity manager
        $this->entityManager->clear();
    }
}
