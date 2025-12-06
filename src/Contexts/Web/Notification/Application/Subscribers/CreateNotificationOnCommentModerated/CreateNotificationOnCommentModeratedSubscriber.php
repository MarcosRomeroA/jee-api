<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnCommentModerated;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use App\Contexts\Web\Post\Domain\CommentRepository;
use App\Contexts\Web\Post\Domain\Events\CommentModeratedDomainEvent;

final readonly class CreateNotificationOnCommentModeratedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private CommentRepository $commentRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(CommentModeratedDomainEvent $event): void
    {
        try {
            $comment = $this->commentRepository->findById($event->getAggregateId());
        } catch (\Exception $e) {
            return;
        }

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::COMMENT_MODERATED,
        );

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $comment->getUser(),
            null,
            $comment->getPost(),
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish($notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [CommentModeratedDomainEvent::class];
    }
}
