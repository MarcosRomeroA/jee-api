<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers\CreateNotificationOnTournamentRequest;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Events\TournamentRequestCreatedDomainEvent;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\User\Domain\User;

final readonly class CreateNotificationOnTournamentRequestSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private TournamentRepository $tournamentRepository,
        private TeamRepository $teamRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(TournamentRequestCreatedDomainEvent $event): void
    {
        $tournament = $this->tournamentRepository->findById($event->tournamentId());
        $team = $this->teamRepository->findById($event->teamId());

        $creator = $team->getCreator();
        if ($creator === null) {
            return; // No creator to use as requester
        }

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::TOURNAMENT_REQUEST_RECEIVED,
        );

        // Notificar al responsable del torneo
        $this->createNotification($notificationType, $tournament->getResponsible(), $creator);
    }

    private function createNotification(
        NotificationType $notificationType,
        User $userToNotify,
        User $requester,
    ): void {
        $notification = Notification::create(
            Uuid::random(),
            $notificationType,
            $userToNotify,
            $requester,
        );

        $this->notificationRepository->save($notification);
        $this->bus->publish($notification->pullDomainEvents());
    }

    public static function subscribedTo(): array
    {
        return [TournamentRequestCreatedDomainEvent::class];
    }
}
