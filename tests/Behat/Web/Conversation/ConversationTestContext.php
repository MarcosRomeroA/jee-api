<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\Conversation;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Web\Conversation\Domain\ValueObject\ContentValue;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Participant\Domain\Participant;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class ConversationTestContext implements Context
{
    // IDs de usuarios de la migración Version20251119000001
    private const USER1_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const USER2_ID = '550e8400-e29b-41d4-a716-446655440002';

    private const CONVERSATION_ID = "550e8400-e29b-41d4-a716-446655440040";
    private const PARTICIPANT1_ID = "550e8400-e29b-41d4-a716-446655440041";
    private const PARTICIPANT2_ID = "550e8400-e29b-41d4-a716-446655440042";
    private const MESSAGE_ID = "550e8400-e29b-41d4-a716-446655440050";

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /** @BeforeScenario @conversation */
    public function createTestData(): void
    {
        // NO limpiar antes de crear - solo limpiar mensajes específicos
        $this->cleanupMessages();

        // Crear notification_type si no existe usando Doctrine
        $notificationType = $this->entityManager
            ->getRepository(NotificationType::class)
            ->findOneBy(["name" => "new_message"]);

        if (!$notificationType) {
            $notificationType = NotificationType::create(
                new Uuid("550e8400-e29b-41d4-a716-446655440099"),
                "new_message",
            );
            $this->entityManager->persist($notificationType);
            $this->entityManager->flush();
        }

        // Los usuarios globales ya existen, solo obtenerlos
        $user1 = $this->userRepository->findById(
            new Uuid(self::USER1_ID),
        );

        $user2 = $this->userRepository->findById(
            new Uuid(self::USER2_ID),
        );

        // Verificar si la conversación ya existe
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        $conversationExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM conversation WHERE id = :id",
            ["id" => self::CONVERSATION_ID],
        );

        // Verificar si los participantes existen
        $participantsCount = $connection->fetchOne(
            "SELECT COUNT(*) FROM participant WHERE conversation_id = :id",
            ["id" => self::CONVERSATION_ID],
        );

        if ($conversationExists > 0 && $participantsCount >= 2) {
            // La conversación y participantes ya existen, no hacer nada
            return;
        }

        // Si la conversación existe pero no tiene participantes, crearlos
        if ($conversationExists > 0 && $participantsCount < 2) {
            // Eliminar participantes existentes
            $connection->executeStatement(
                "DELETE FROM participant WHERE conversation_id = :id",
                ["id" => self::CONVERSATION_ID]
            );

            // Obtener la conversación existente
            $conversation = $this->entityManager->find(Conversation::class, new Uuid(self::CONVERSATION_ID));

            // Agregar participantes
            $participant1 = Participant::create(
                new Uuid(self::PARTICIPANT1_ID),
                $conversation,
                $user1,
                true,
            );
            $this->entityManager->persist($participant1);

            $participant2 = Participant::create(
                new Uuid(self::PARTICIPANT2_ID),
                $conversation,
                $user2,
                false,
            );
            $this->entityManager->persist($participant2);

            $this->entityManager->flush();
            return;
        }

        // Crear una conversación de prueba
        $conversation = Conversation::create(new Uuid(self::CONVERSATION_ID));

        // Agregar participantes a la conversación
        $participant1 = Participant::create(
            new Uuid(self::PARTICIPANT1_ID),
            $conversation,
            $user1,
            true,
        );
        $conversation->addParticipant($participant1);
        $this->entityManager->persist($participant1);

        $participant2 = Participant::create(
            new Uuid(self::PARTICIPANT2_ID),
            $conversation,
            $user2,
            false,
        );
        $conversation->addParticipant($participant2);
        $this->entityManager->persist($participant2);

        $this->entityManager->persist($conversation);

        // NO crear mensaje inicial - los tests crearán sus propios mensajes
        // Esto evita conflictos de IDs con los tests

        $this->entityManager->flush();
    }

    /** @AfterScenario @conversation */
    public function cleanupTestData(): void
    {
        $this->cleanupMessages();

        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Limpiar participantes
            $connection->executeStatement(
                "DELETE FROM participant WHERE conversation_id = :id",
                ["id" => self::CONVERSATION_ID]
            );
        } catch (\Exception $e) {
        }

        try {
            // Limpiar conversación
            $connection->executeStatement(
                "DELETE FROM conversation WHERE id = :id",
                ["id" => self::CONVERSATION_ID]
            );
        } catch (\Exception $e) {
        }

        try {
            // Limpiar notification_type de test
            $connection->executeStatement(
                "DELETE FROM notification_type WHERE id = :id",
                ["id" => "550e8400-e29b-41d4-a716-446655440099"]
            );
        } catch (\Exception $e) {
        }

        $this->entityManager->clear();
    }

    private function cleanupMessages(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        // IDs de mensajes usados en los tests
        $messageIds = [
            '550e8400-e29b-41d4-a716-446655440050', // MESSAGE_ID constante
            '550e8400-e29b-41d4-a716-446655440060', // message_create test
            '550e8400-e29b-41d4-a716-446655440051', // message_create test
            '550e8400-e29b-41d4-a716-446655440052', // message_create test
            '550e8400-e29b-41d4-a716-446655440053', // message_create test
            '950e8400-e29b-41d4-a716-446655440999', // notification_mercure test
        ];

        try {
            // Primero limpiar la referencia last_message_id en conversation para evitar FK constraint
            $connection->executeStatement(
                "UPDATE conversation SET last_message_id = NULL WHERE id = :conversationId",
                ["conversationId" => self::CONVERSATION_ID]
            );

            // Limpiar notificaciones de los mensajes específicos
            foreach ($messageIds as $messageId) {
                $connection->executeStatement(
                    "DELETE FROM notification WHERE message_id = :messageId",
                    ["messageId" => $messageId]
                );
            }

            // Limpiar notificaciones de mensajes de la conversación
            $connection->executeStatement(
                "DELETE n FROM notification n INNER JOIN message m ON n.message_id = m.id WHERE m.conversation_id = :conversationId",
                ["conversationId" => self::CONVERSATION_ID],
            );

            // Limpiar mensajes específicos
            foreach ($messageIds as $messageId) {
                $connection->executeStatement(
                    "DELETE FROM message WHERE id = :messageId",
                    ["messageId" => $messageId]
                );
            }

            // Limpiar mensajes de la conversación
            $connection->executeStatement(
                "DELETE FROM message WHERE conversation_id = :conversationId",
                ["conversationId" => self::CONVERSATION_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }
    }
}
