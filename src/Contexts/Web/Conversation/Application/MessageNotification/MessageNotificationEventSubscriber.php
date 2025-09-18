<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\MessageNotification;

use Psr\Log\LoggerInterface;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Conversation\Domain\Events\MessageCreatedEvent;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;

readonly class MessageNotificationEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private MessageRepository $messageRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {}

    public function __invoke(MessageCreatedEvent $event): void
    {
        $message = $this->messageRepository->findByIdOrFail($event->getAggregateId());

        $notificationType = $this->notificationTypeRepository->findByName(NotificationType::NEW_MESSAGE);
        
        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $message->getConversation()->getOtherParticipant($message->getUser())->getUser(),
            null,
            null,
            $message
        );

        $this->notificationRepository->save($notification);

        $this->bus->publish(...$message->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [MessageCreatedEvent::class];
    }
}
