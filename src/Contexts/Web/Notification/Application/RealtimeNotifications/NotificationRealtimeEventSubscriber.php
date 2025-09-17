<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\RealtimeNotifications;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\Notification\Domain\Event\NotificationCreatedEvent;
use App\Contexts\Web\Notification\Application\Shared\NotificationResponse;

readonly class NotificationCreatedEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private HubInterface $hub,
        private NotificationRepository $notificationRepository,
        private MessageRepository $messageRepository,
    ) {}

    public function __invoke(NotificationCreatedEvent $event): void
    {
        $notification = $this->notificationRepository->findByIdOrFail($event->getAggregateId());

        $update = new Update(
            sprintf('notification/%s', $event->toPrimitives()['userNotifiableId']),
            json_encode(NotificationResponse::fromEntity($notification)->toArray())
        );

        $this->hub->publish($update);
    }

    public static function subscribedTo(): array
    {
        return [NotificationCreatedEvent::class];
    }
}
