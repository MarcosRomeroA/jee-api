<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnTournamentRequestAccepted;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Events\TournamentRequestAcceptedDomainEvent;
use App\Contexts\Web\User\Domain\User;

final readonly class CreateNotificationOnTournamentRequestAcceptedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private TeamRepository $teamRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(TournamentRequestAcceptedDomainEvent $event): void
    {
        $team = $this->teamRepository->findById($event->teamId());
        $tournamentId = $event->tournamentId()->value();

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::TOURNAMENT_REQUEST_ACCEPTED,
        );

        if ($notificationType === null) {
            return;
        }

        // Notificar al creador del equipo
        $teamCreator = $team->getCreator();
        if ($teamCreator !== null) {
            $this->createNotification($notificationType, $teamCreator, $tournamentId);
        }

        // Notificar al líder del equipo si es diferente al creador
        $teamLeader = $team->getLeader();
        if ($teamLeader !== null && ($teamCreator === null || !$teamLeader->getId()->equals($teamCreator->getId()))) {
            $this->createNotification($notificationType, $teamLeader, $tournamentId);
        }
    }

    private function createNotification(
        NotificationType $notificationType,
        User $userToNotify,
        string $tournamentId,
    ): void {
        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $userToNotify,
            null,
            null,
            null,
            null,
            $tournamentId,
        );

        $this->notificationRepository->save($notification);
        $this->bus->publish($notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [TournamentRequestAcceptedDomainEvent::class];
    }
}
