<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnPostLiked;

use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Post\Domain\Events\PostLikedDomainEvent;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;

final readonly class CreateNotificationOnPostLikedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostRepository $postRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(PostLikedDomainEvent $event): void
    {
        $post = $this->postRepository->findById($event->getAggregateId());

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::POST_LIKED,
        );

        $userLiker = $this->userRepository->findById($event->userLikerId());

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $post->getUser(),
            $userLiker,
            $post,
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish(...$notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [PostLikedDomainEvent::class];
    }
}
