<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnPostCommented;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Post\Domain\Events\PostCommentedDomainEvent;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;

readonly class CreateNotificationOnPostCommentedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostRepository $postRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(PostCommentedDomainEvent $event): void
    {
        $post = $this->postRepository->findById($event->getAggregateId());
        $userCommenter = $this->userRepository->findById($event->userCommenterId());
        $postAuthor = $post->getUser();

        // No notificar si el usuario comenta en su propio post
        if ($postAuthor->getId()->equals($userCommenter->getId())) {
            return;
        }

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::POST_COMMENTED,
        );

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $postAuthor,
            $userCommenter,
            $post,
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish($notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [PostCommentedDomainEvent::class];
    }
}
