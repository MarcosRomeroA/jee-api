<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnPostShared;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;

readonly class CreateNotificationOnPostSharedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostRepository $postRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(PostCreatedDomainEvent $event): void
    {
        // Try to find the post - it might not exist if processed asynchronously after deletion
        $post = $this->postRepository->find($event->getAggregateId());

        if (!$post) {
            return;
        }

        if (!$post->getSharedPostId()) {
            return;
        }

        $sharedPost = $this->postRepository->findById($post->getSharedPostId());

        $notificationType = $this->notificationTypeRepository->findByName(NotificationType::POST_SHARED);

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $sharedPost->getUser(),
            $post->getUser(),
            $post,
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish($notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [PostCreatedDomainEvent::class];
    }
}
