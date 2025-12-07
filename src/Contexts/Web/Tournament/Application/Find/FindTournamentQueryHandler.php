<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentResponse;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final class FindTournamentQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly TournamentFinder $finder,
        private readonly string $cdnBaseUrl,
        private readonly TournamentTeamRepository $tournamentTeamRepository,
    ) {
    }

    public function __invoke(FindTournamentQuery $query): TournamentResponse
    {
        $tournamentId = new Uuid($query->id);
        $tournament = $this->finder->find($tournamentId);

        $isUserRegistered = false;
        if ($query->currentUserId !== null) {
            $isUserRegistered = $this->tournamentTeamRepository->isUserRegisteredInTournament(
                $tournamentId,
                new Uuid($query->currentUserId)
            );
        }

        return TournamentResponse::fromTournament($tournament, $this->cdnBaseUrl, $isUserRegistered);
    }
}
