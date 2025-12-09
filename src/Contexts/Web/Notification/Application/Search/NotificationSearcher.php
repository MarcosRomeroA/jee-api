<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Search;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Application\Shared\NotificationCollectionResponse;
use App\Contexts\Web\Notification\Application\Shared\NotificationResponse;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final readonly class NotificationSearcher
{
    public function __construct(
        private NotificationRepository $repository,
        private TeamRepository $teamRepository,
        private TournamentRepository $tournamentRepository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(?array $criteria): NotificationCollectionResponse
    {
        $notifications = $this->repository->searchByCriteria($criteria);

        $response = [];
        foreach ($notifications as $notification) {
            $team = $this->findTeam($notification->getTeamId());
            $tournament = $this->findTournament($notification->getTournamentId());

            $profileImage = $this->buildProfileImage($notification, $team, $tournament);
            $teamName = $team?->getName();
            $tournamentName = $tournament?->getName();

            $response[] = NotificationResponse::fromEntity(
                $notification,
                $profileImage,
                $teamName,
                $tournamentName,
            );
        }

        $total = $this->repository->countByCriteria($criteria);

        return new NotificationCollectionResponse($response, $criteria, $total);
    }

    private function findTeam(?string $teamId): ?Team
    {
        if ($teamId === null || $teamId === '') {
            return null;
        }

        try {
            return $this->teamRepository->findById(new Uuid($teamId));
        } catch (\Throwable) {
            return null;
        }
    }

    private function findTournament(?string $tournamentId): ?Tournament
    {
        if ($tournamentId === null || $tournamentId === '') {
            return null;
        }

        try {
            return $this->tournamentRepository->findById(new Uuid($tournamentId));
        } catch (\Throwable) {
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
            NotificationType::TOURNAMENT_REQUEST_ACCEPTED => $tournament?->getBackgroundImageUrl($this->cdnBaseUrl),
            default => $this->getUserProfileImage($notification),
        };
    }

    private function getUserProfileImage(Notification $notification): ?string
    {
        $user = $notification->getUser();
        if ($user === null) {
            return null;
        }

        return $user->getAvatarUrl(128, $this->cdnBaseUrl);
    }
}
