<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\RealtimeNotifications;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Notification\Domain\Event\NotificationCreatedEvent;
use App\Contexts\Web\Notification\Application\Shared\NotificationResponse;

final readonly class NotificationRealtimeEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private HubInterface $hub,
        private NotificationRepository $notificationRepository,
    ) {}

    public function __invoke(NotificationCreatedEvent $event): void
    {
        $notification = $this->notificationRepository->findByIdOrFail($event->getAggregateId());

        $update = new Update(
            $_ENV['APP_URL'].'/notification/' . $event->toPrimitives()['userIdToNotify'],
            json_encode(NotificationResponse::fromEntity($notification)->toArray())
        );

        $this->hub->publish($update);
    }

    public static function subscribedTo(): array
    {
        return [NotificationCreatedEvent::class];
    }
}
