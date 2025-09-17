<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SharedPostNotification;

use Psr\Log\LoggerInterface;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;

readonly class SharedPostNotificationEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private PostRepository $postRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {}

    public function __invoke(PostCreatedDomainEvent $event): void
    {
        $post = $this->postRepository->findById($event->getAggregateId());

        if (!$post->getSharedPost()) {
            return;
        }

        $notificationType = $this->notificationTypeRepository->findByName(NotificationType::POST_SHARED);

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $post->getUser(),
            $post->getSharedPost()->getUser(),
            $post->getSharedPost(),
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish(...$post->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [PostCreatedDomainEvent::class];
    }
}
