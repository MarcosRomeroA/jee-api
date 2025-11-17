<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindTeamGames;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\TeamGameCollectionResponse;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamGameRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamGamesFinder
{
    public function __construct(
        private TeamRepository $teamRepository,
        private TeamGameRepository $teamGameRepository
    ) {
    }

    public function __invoke(Uuid $teamId): TeamGameCollectionResponse
    {
        $this->ensureTeamExists($teamId);

        $teamGames = $this->teamGameRepository->findByTeam($teamId);

        return TeamGameCollectionResponse::fromTeamGames($teamGames);
    }

    private function ensureTeamExists(Uuid $teamId): void
    {
        $team = $this->teamRepository->findById($teamId);

        if ($team === null) {
            throw new TeamNotFoundException($teamId->value());
        }
    }
}
