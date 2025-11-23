<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\RequestAccess;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentRequestAlreadyExistsException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentRequest;
use App\Contexts\Web\Tournament\Domain\TournamentRequestRepository;

final readonly class TournamentAccessRequester
{
    public function __construct(
        private TournamentRepository $tournamentRepository,
        private TeamRepository $teamRepository,
        private TournamentRequestRepository $requestRepository,
    ) {
    }

    public function __invoke(Uuid $tournamentId, Uuid $teamId): void
    {
        $tournament = $this->tournamentRepository->findById($tournamentId);

        if ($tournament === null) {
            throw new TournamentNotFoundException($tournamentId->value());
        }

        $team = $this->teamRepository->findById($teamId);

        if ($team === null) {
            throw new TeamNotFoundException($teamId->value());
        }

        $existingRequest = $this->requestRepository->findPendingByTournamentAndTeam(
            $tournamentId,
            $teamId
        );

        if ($existingRequest !== null) {
            throw new TournamentRequestAlreadyExistsException($tournamentId->value(), $teamId->value());
        }

        $request = new TournamentRequest(
            Uuid::random(),
            $tournament,
            $team
        );

        $this->requestRepository->save($request);
    }
}
