<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FollowerNotification;

use Psr\Log\LoggerInterface;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use App\Contexts\Web\User\Domain\Events\FollowerCreatedDomainEvent;

readonly class FollowerNotificationEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private UserRepository $userRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {}

    public function __invoke(FollowerCreatedDomainEvent $event): void
    {
        $follower = $this->userRepository->findById(new Uuid($event->toPrimitives()['followerId']));
        $followed = $this->userRepository->findById(new Uuid($event->toPrimitives()['followedId']));

        $notificationType = $this->notificationTypeRepository->findByName(NotificationType::NEW_FOLLOWER);
        
        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $followed,
            $follower,
            null,
            null
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish(...$notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [FollowerCreatedDomainEvent::class];
    }
}

