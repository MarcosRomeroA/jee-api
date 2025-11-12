<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchMatches;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentMatchRepository;

final class MatchesSearcher
{
    public function __construct(
        private readonly TournamentMatchRepository $matchRepository
    ) {
    }

    public function searchByTournament(Uuid $tournamentId): array
    {
        return $this->matchRepository->findByTournamentId($tournamentId);
    }

    public function searchByTournamentAndRound(Uuid $tournamentId, int $round): array
    {
        return $this->matchRepository->findByTournamentIdAndRound($tournamentId, $round);
    }
}

