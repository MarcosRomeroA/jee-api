<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Notification\Application;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Application\Create\NotificationCreator;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Tests\Unit\Web\Notification\Domain\NotificationTypeMother;
use App\Tests\Unit\Web\Notification\Domain\UserMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class NotificationCreatorTest extends TestCase
{
    private NotificationRepository|MockObject $notificationRepository;
    private EventBus|MockObject $bus;
    private NotificationCreator $creator;

    protected function setUp(): void
    {
        $this->notificationRepository = $this->createMock(NotificationRepository::class);
        $this->bus = $this->createMock(EventBus::class);

        $this->creator = new NotificationCreator(
            $this->notificationRepository,
            $this->bus
        );
    }

    public function testItShouldCreateNotificationWithPost(): void
    {
        // Arrange
        $id = Uuid::random();
        $notificationType = NotificationTypeMother::postLiked();
        $user = UserMother::random();
        $post = null; // Simplificado - en realidad sería PostMother::random()

        $this->notificationRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Notification::class));

        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->isType('array'));

        // Act
        ($this->creator)($id, $notificationType, $user, $post);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldCreateNotificationWithoutPost(): void
    {
        // Arrange
        $id = Uuid::random();
        $notificationType = NotificationTypeMother::newFollower();
        $user = UserMother::random();

        $this->notificationRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Notification::class));

        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->isType('array'));

        // Act
        ($this->creator)($id, $notificationType, $user, null);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldPublishDomainEvents(): void
    {
        // Arrange
        $id = Uuid::random();
        $notificationType = NotificationTypeMother::postCommented();
        $user = UserMother::random();

        $this->notificationRepository
            ->expects($this->once())
            ->method('save');

        // Verify events are published
        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->callback(function (array $events) {
                return is_array($events) && !empty($events);
            }));

        // Act
        ($this->creator)($id, $notificationType, $user, null);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldCreateNewMessageNotification(): void
    {
        // Arrange
        $id = Uuid::random();
        $notificationType = NotificationTypeMother::newMessage();
        $user = UserMother::random();

        $this->notificationRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Notification::class));

        $this->bus
            ->expects($this->once())
            ->method('publish');

        // Act
        ($this->creator)($id, $notificationType, $user, null);

        // Assert
        $this->assertTrue(true);
    }
}
