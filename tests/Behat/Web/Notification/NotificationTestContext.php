<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Notification;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class NotificationTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @notification */
    public function createTestData(): void
    {
        // Crear un usuario de prueba
        $user = new User(
            new Uuid('550e8400-e29b-41d4-a716-446655440001'),
            new FirstnameValue('John'),
            new LastnameValue('Doe'),
            new UsernameValue('testuser'),
            new EmailValue('test@example.com'),
            new PasswordValue(password_hash('password123', PASSWORD_BCRYPT))
        );
        $this->entityManager->persist($user);

        // Crear un tipo de notificación
        $notificationType = new NotificationType(
            new Uuid('550e8400-e29b-41d4-a716-446655440035'),
            'test_notification',
            'Test Notification Type'
        );
        $this->entityManager->persist($notificationType);

        // Crear una notificación de prueba
        $notification = new Notification(
            new Uuid('550e8400-e29b-41d4-a716-446655440030'),
            $notificationType,
            $user, // userToNotify
            null,  // user (quien genera la notificación)
            null,  // post
            null   // message
        );
        $this->entityManager->persist($notification);

        $this->entityManager->flush();
    }

    /** @AfterScenario @notification */
    public function cleanupTestData(): void
    {
        // Limpiar notificaciones
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Notification\Domain\Notification')->execute();

        // Limpiar tipos de notificación
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Notification\Domain\NotificationType')->execute();

        // Limpiar usuarios
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\User\Domain\User')->execute();
    }
}

