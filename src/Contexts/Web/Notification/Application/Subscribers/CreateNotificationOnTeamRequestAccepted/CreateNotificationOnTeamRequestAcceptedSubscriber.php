<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnTeamRequestAccepted;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use App\Contexts\Web\Team\Domain\Events\TeamRequestAcceptedDomainEvent;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class CreateNotificationOnTeamRequestAcceptedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private UserRepository $userRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(TeamRequestAcceptedDomainEvent $event): void
    {
        $acceptedUser = $this->userRepository->findById($event->userId());
        $teamId = $event->teamId()->value();

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::TEAM_REQUEST_ACCEPTED,
        );

        if ($notificationType === null) {
            return;
        }

        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $acceptedUser,
            null,
            null,
            null,
            $teamId,
        );

        $this->notificationRepository->save($notification);
        $this->bus->publish($notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [TeamRequestAcceptedDomainEvent::class];
    }
}
