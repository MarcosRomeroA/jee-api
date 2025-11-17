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
            $tournament = $tournamentTeam->tournament();

            $tournaments[] = $this->buildTournamentArray($tournament);
        }

        return $tournaments;
    }

    private function buildTournamentArray($tournament): array
    {
        return [
            'id' => $tournament->id()->value(),
            'name' => $tournament->name(),
            'description' => $tournament->description(),
            'isOfficial' => $tournament->isOfficial(),
            'maxTeams' => $tournament->maxTeams(),
            'startAt' => $tournament->startAt()?->format('Y-m-d\TH:i:s\Z'),
            'endAt' => $tournament->endAt()?->format('Y-m-d\TH:i:s\Z'),
            'game' => [
                'id' => $tournament->game()->id()->value(),
                'name' => $tournament->game()->name(),
            ],
        ];
    }
}
