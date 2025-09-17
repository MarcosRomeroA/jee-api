<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\PostLikedNotification;

use Psr\Log\LoggerInterface;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Post\Domain\Events\PostLikedDomainEvent;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;

final readonly class PostLikedNotificationEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private PostRepository $postRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    )
    {
    }

    public function __invoke(PostLikedDomainEvent $event): void
    {
        $post = $this->postRepository->findById($event->getAggregateId());

        $notificationType = $this->notificationTypeRepository->findByName(NotificationType::POST_LIKED);

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $post->getUser(),
            null,
            $post,
            null,
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish(...$notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [PostLikedDomainEvent::class];
    }
}
