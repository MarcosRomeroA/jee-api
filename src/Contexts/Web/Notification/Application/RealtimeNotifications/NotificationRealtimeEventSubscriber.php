<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\RealtimeNotifications;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Application\Shared\NotificationResponse;
use App\Contexts\Web\Notification\Domain\Event\NotificationCreatedEvent;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class NotificationRealtimeEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private HubInterface $hub,
        private NotificationRepository $notificationRepository,
        private TeamRepository $teamRepository,
        private TournamentRepository $tournamentRepository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(NotificationCreatedEvent $event): void
    {
        $notification = $this->notificationRepository->findById(
            $event->getAggregateId(),
        );

        $team = $this->findTeam($notification->getTeamId());
        $tournament = $this->findTournament($notification->getTournamentId());

        $profileImage = $this->buildProfileImage($notification, $team, $tournament);
        $teamName = $team?->getName();
        $tournamentName = $tournament?->getName();

        $update = new Update(
            $_ENV["APP_URL"] .
                "/notification/" .
                $event->toPrimitives()["userIdToNotify"],
            json_encode(
                NotificationResponse::fromEntity(
                    $notification,
                    $profileImage,
                    $teamName,
                    $tournamentName,
                )->toArray(),
            ),
        );

        try {
            $this->hub->publish($update);
        } catch (\Exception $e) {
            // Log error but don't fail the request - Mercure is optional for tests
            error_log(
                "Failed to publish notification to Mercure: " .
                    $e->getMessage(),
            );
        }
    }

    private function findTeam(?string $teamId): ?Team
    {
        if ($teamId === null) {
            return null;
        }

        try {
            return $this->teamRepository->findById(new Uuid($teamId));
        } catch (\Exception) {
            return null;
        }
    }

    private function findTournament(?string $tournamentId): ?Tournament
    {
        if ($tournamentId === null) {
            return null;
        }

        try {
            return $this->tournamentRepository->findById(new Uuid($tournamentId));
        } catch (\Exception) {
            return null;
        }
    }

    private function buildProfileImage(
        Notification $notification,
        ?Team $team,
        ?Tournament $tournament,
    ): ?string {
        $type = $notification->getNotificationType()->getName();

        return match ($type) {
            NotificationType::TEAM_REQUEST_ACCEPTED => $team?->getImageUrl($this->cdnBaseUrl),
            NotificationType::TOURNAMENT_REQUEST_RECEIVED => $team?->getImageUrl($this->cdnBaseUrl),
            NotificationType::TOURNAMENT_REQUEST_ACCEPTED => $tournament?->getImageUrl($this->cdnBaseUrl),
            default => $notification->getUser()?->getAvatarUrl(128, $this->cdnBaseUrl),
        };
    }

    public static function subscribedTo(): array
    {
        return [NotificationCreatedEvent::class];
    }
}
