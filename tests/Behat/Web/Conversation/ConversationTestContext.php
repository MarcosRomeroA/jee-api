<?php declare(strict_types=1);

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
use App\Tests\Behat\Shared\Fixtures\TestUsers;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class ConversationTestContext implements Context
{
    private const CONVERSATION_ID = "550e8400-e29b-41d4-a716-446655440040";
    private const PARTICIPANT1_ID = "550e8400-e29b-41d4-a716-446655440041";
    private const PARTICIPANT2_ID = "550e8400-e29b-41d4-a716-446655440042";
    private const MESSAGE_ID = "550e8400-e29b-41d4-a716-446655440050";

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {}

    /** @BeforeScenario @conversation */
    public function createTestData(): void
    {
        // Crear notification_type si no existe usando Doctrine
        $notificationType = $this->entityManager
            ->getRepository(NotificationType::class)
            ->findOneBy(["name" => "NEW_MESSAGE"]);

        if (!$notificationType) {
            $notificationType = NotificationType::create(
                new Uuid("550e8400-e29b-41d4-a716-446655440099"),
                "NEW_MESSAGE",
            );
            $this->entityManager->persist($notificationType);
            $this->entityManager->flush();
        }

        // Obtener o crear usuario 1
        try {
            $user1 = $this->userRepository->findById(
                new Uuid(TestUsers::USER1_ID),
            );
        } catch (\Exception $e) {
            $user1 = User::create(
                new Uuid(TestUsers::USER1_ID),
                new FirstnameValue(TestUsers::USER1_FIRSTNAME),
                new LastnameValue(TestUsers::USER1_LASTNAME),
                new UsernameValue(TestUsers::USER1_USERNAME),
                new EmailValue(TestUsers::USER1_EMAIL),
                new PasswordValue(TestUsers::USER1_PASSWORD),
            );
            $this->userRepository->save($user1);
        }

        // Obtener o crear usuario 2
        try {
            $user2 = $this->userRepository->findById(
                new Uuid(TestUsers::USER2_ID),
            );
        } catch (\Exception $e) {
            $user2 = User::create(
                new Uuid(TestUsers::USER2_ID),
                new FirstnameValue(TestUsers::USER2_FIRSTNAME),
                new LastnameValue(TestUsers::USER2_LASTNAME),
                new UsernameValue(TestUsers::USER2_USERNAME),
                new EmailValue(TestUsers::USER2_EMAIL),
                new PasswordValue(TestUsers::USER2_PASSWORD),
            );
            $this->userRepository->save($user2);
        }

        // Verificar si la conversación ya existe
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        $conversationExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM conversation WHERE id = :id",
            ["id" => self::CONVERSATION_ID],
        );

        if ($conversationExists > 0) {
            // La conversación ya existe, no hacer nada
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

        // Crear un mensaje de prueba
        $message = new Message(
            new Uuid(self::MESSAGE_ID),
            $conversation,
            $user1,
            new ContentValue("Hello, this is a test message!"),
        );
        $this->entityManager->persist($message);

        $this->entityManager->flush();
    }

    /** @AfterScenario @conversation */
    public function cleanupTestData(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        try {
            // Primero eliminar notificaciones asociadas a los mensajes
            $connection->executeStatement(
                "DELETE FROM notification WHERE message_id IN (SELECT id FROM message WHERE conversation_id = :conversationId)",
                ["conversationId" => self::CONVERSATION_ID],
            );

            // Luego limpiar mensajes de la conversación (pero mantener la conversación y participantes)
            $connection->executeStatement(
                "DELETE FROM message WHERE conversation_id = :conversationId",
                ["conversationId" => self::CONVERSATION_ID],
            );
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        // NO eliminar la conversación ni los participantes para que estén disponibles para otros tests
        // Solo limpiar al final de todos los tests del contexto de conversación

        // NO eliminar usuarios - son compartidos entre contextos
        // Limpiar el entity manager
        $this->entityManager->clear();
    }
}
