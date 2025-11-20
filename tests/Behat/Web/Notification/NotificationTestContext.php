<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\Notification;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\User\Domain\UserRepository;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class NotificationTestContext implements Context
{
    // IDs de usuarios de la migración Version20251119000001
    private const USER1_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const USER2_ID = '550e8400-e29b-41d4-a716-446655440002';
    private const USER3_ID = '550e8400-e29b-41d4-a716-446655440003';

    private const TEST_NOTIFICATION_TYPE_ID = "550e8400-e29b-41d4-a716-446655440035";
    private const TEST_NOTIFICATION_ID = "550e8400-e29b-41d4-a716-446655440036";

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /** @BeforeScenario @notification */
    public function createTestData(): void
    {
        // Obtener el usuario compartido (creado por la migración Version20251119000001)
        $user = $this->userRepository->findById(
            new Uuid(self::USER1_ID),
        );

        // Verificar si el tipo de notificación ya existe
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        $notificationTypeExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM notification_type WHERE id = :id",
            ["id" => self::TEST_NOTIFICATION_TYPE_ID],
        );

        if (!$notificationTypeExists) {
            // Crear un tipo de notificación
            $notificationType = new NotificationType(
                new Uuid(self::TEST_NOTIFICATION_TYPE_ID),
                "test_notification",
                "Test Notification Type",
            );
            $this->entityManager->persist($notificationType);
            $this->entityManager->flush();
        } else {
            // Obtener el tipo existente
            $notificationType = $this->entityManager
                ->getRepository(NotificationType::class)
                ->find(self::TEST_NOTIFICATION_TYPE_ID);
        }

        // Verificar si la notificación ya existe
        $notificationExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM notification WHERE id = :id",
            ["id" => self::TEST_NOTIFICATION_ID],
        );

        if ($notificationExists) {
            // La notificación ya existe, no hacer nada
            return;
        }

        // Crear una notificación de prueba
        $notification = new Notification(
            new Uuid(self::TEST_NOTIFICATION_ID),
            $notificationType,
            $user, // userToNotify
            null, // user (quien genera la notificación)
            null, // post
            null, // message
        );
        $this->entityManager->persist($notification);

        $this->entityManager->flush();
    }

    /** @AfterScenario @notification */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Limpiar notificaciones específicas
            $connection->executeStatement(
                "DELETE FROM notification WHERE id = :notificationId",
                ["notificationId" => self::TEST_NOTIFICATION_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar notificaciones del usuario de prueba
            $connection->executeStatement(
                "DELETE FROM notification WHERE user_to_notify_id = :userId",
                ["userId" => self::USER1_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar tipos de notificación específicos
            $connection->executeStatement(
                "DELETE FROM notification_type WHERE id = :typeId",
                ["typeId" => self::TEST_NOTIFICATION_TYPE_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            // Limpiar relaciones de follow creadas durante tests de notificaciones
            // (el test de Mercure NEW_FOLLOWER crea un follow de tester2 a tester1)
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
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // NO eliminar el usuario - es compartido entre contextos
        // Limpiar el entity manager
        $this->entityManager->clear();
    }
}
