<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnTeamRequest;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use App\Contexts\Web\Team\Domain\Events\TeamRequestCreatedDomainEvent;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class CreateNotificationOnTeamRequestSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private TeamRepository $teamRepository,
        private UserRepository $userRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(TeamRequestCreatedDomainEvent $event): void
    {
        $team = $this->teamRepository->findById($event->teamId());
        $requester = $this->userRepository->findById($event->userId());
        $teamId = $event->teamId()->value();

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::TEAM_REQUEST_RECEIVED,
        );

        // Notificar al creador del equipo
        $creator = $team->getCreator();
        if ($creator !== null) {
            $this->createNotification($notificationType, $creator, $requester, $teamId);
        }

        // Notificar al líder si existe y es diferente al creador
        $leader = $team->getLeader();
        if ($leader !== null && ($creator === null || !$leader->getId()->equals($creator->getId()))) {
            $this->createNotification($notificationType, $leader, $requester, $teamId);
        }
    }

    private function createNotification(
        NotificationType $notificationType,
        User $userToNotify,
        User $requester,
        string $teamId,
    ): void {
        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $userToNotify,
            $requester,
            null,
            null,
            $teamId,
        );

        $this->notificationRepository->save($notification);
        $this->bus->publish($notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [TeamRequestCreatedDomainEvent::class];
    }
}
