<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnPostModerated;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use App\Contexts\Web\Post\Domain\Events\PostModeratedDomainEvent;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class CreateNotificationOnPostModeratedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostRepository $postRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(PostModeratedDomainEvent $event): void
    {
        try {
            $post = $this->postRepository->findById($event->getAggregateId());
        } catch (\Exception $e) {
            return;
        }

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::POST_MODERATED,
        );

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $post->getUser(),
            null,
            $post,
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish($notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [PostModeratedDomainEvent::class];
    }
}
