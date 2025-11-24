<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindTournaments;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final readonly class UserTournamentsFinder
{
    public function __construct(
        private TournamentTeamRepository $tournamentTeamRepository
    ) {
    }

    public function __invoke(Uuid $userId): array
    {
        $tournamentTeams = $this->tournamentTeamRepository->findByUserId($userId);

        $tournaments = [];
        foreach ($tournamentTeams as $tournamentTeam) {
            $tournament = $tournamentTeam->getTournament();

            $tournaments[] = $this->buildTournamentArray($tournament);
        }

        return $tournaments;
    }

    private function buildTournamentArray($tournament): array
    {
        return [
            'id' => $tournament->getId()->value(),
            'name' => $tournament->getName(),
            'description' => $tournament->getDescription(),
            'isOfficial' => $tournament->getIsOfficial(),
            'maxTeams' => $tournament->getMaxTeams(),
            'startAt' => $tournament->getStartAt()?->format('Y-m-d\TH:i:s\Z'),
            'endAt' => $tournament->getEndAt()?->format('Y-m-d\TH:i:s\Z'),
            'game' => [
                'id' => $tournament->getGame()->getId()->value(),
                'name' => $tournament->getGame()->getName(),
            ],
        ];
    }
}
