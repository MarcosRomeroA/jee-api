<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\CommentedPostNotification;

use Psr\Log\LoggerInterface;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\Post\Domain\CommentRepository;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Post\Domain\Events\PostCommentedDomainEvent;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;

readonly class CommentedPostNotificationEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostRepository $postRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
        private CommentRepository $commentRepository,
    ) {}

    public function __invoke(PostCommentedDomainEvent $event): void
    {
        $post = $this->postRepository->findById($event->getAggregateId());

        $notificationType = $this->notificationTypeRepository->findByName(NotificationType::POST_COMMENTED);

        $comment = $this->commentRepository->findById($event->toPrimitives()['commentId']);

        $userCommenter = $comment->getUser();

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $post->getUser(),
            $userCommenter,
            $post,
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish(...$notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [PostCommentedDomainEvent::class];
    }
}
