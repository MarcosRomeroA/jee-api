<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Conversation;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Web\Conversation\Domain\ValueObject\ContentValue;
use App\Contexts\Web\Participant\Domain\Participant;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class ConversationTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @conversation */
    public function createTestData(): void
    {
        // Crear primer usuario (el que ejecuta los tests)
        $user1 = new User(
            new Uuid('550e8400-e29b-41d4-a716-446655440001'),
            new FirstnameValue('John'),
            new LastnameValue('Doe'),
            new UsernameValue('testuser'),
            new EmailValue('test@example.com'),
            new PasswordValue(password_hash('password123', PASSWORD_BCRYPT))
        );
        $this->entityManager->persist($user1);

        // Crear segundo usuario (con quien se conversa)
        $user2 = new User(
            new Uuid('550e8400-e29b-41d4-a716-446655440002'),
            new FirstnameValue('Jane'),
            new LastnameValue('Smith'),
            new UsernameValue('janesmith'),
            new EmailValue('jane@example.com'),
            new PasswordValue(password_hash('password456', PASSWORD_BCRYPT))
        );
        $this->entityManager->persist($user2);

        // Crear una conversación de prueba usando el método estático create
        $conversation = Conversation::create(
            new Uuid('550e8400-e29b-41d4-a716-446655440040')
        );

        // Agregar participantes a la conversación
        $participant1 = new Participant(
            new Uuid('550e8400-e29b-41d4-a716-446655440041'),
            $conversation,
            $user1
        );
        $conversation->addParticipant($participant1);
        $this->entityManager->persist($participant1);

        $participant2 = new Participant(
            new Uuid('550e8400-e29b-41d4-a716-446655440042'),
            $conversation,
            $user2
        );
        $conversation->addParticipant($participant2);
        $this->entityManager->persist($participant2);

        $this->entityManager->persist($conversation);

        // Crear un mensaje de prueba
        $message = new Message(
            new Uuid('550e8400-e29b-41d4-a716-446655440050'),
            $conversation,
            $user1,
            new ContentValue('Hello, this is a test message!')
        );
        $this->entityManager->persist($message);

        $this->entityManager->flush();
    }

    /** @AfterScenario @conversation */
    public function cleanupTestData(): void
    {
        // Limpiar mensajes (esto se limpiará por cascade desde conversation)
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Conversation\Domain\Conversation')->execute();

        // Limpiar usuarios
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\User\Domain\User')->execute();
    }
}
