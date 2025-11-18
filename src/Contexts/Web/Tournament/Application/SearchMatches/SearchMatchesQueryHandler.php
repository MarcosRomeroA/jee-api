<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchMatches;

use App\Contexts\Shared\Domain\Bus\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class SearchMatchesQueryHandler implements QueryHandler
{
    public function __construct(
        private MatchesSearcher $matchesSearcher
    ) {
    }

    public function __invoke(SearchMatchesQuery $query): array
    {
        if ($query->round !== null) {
            return $this->matchesSearcher->searchByTournamentAndRound(
                new Uuid($query->tournamentId),
                $query->round
            );
        }

        return $this->matchesSearcher->searchByTournament(new Uuid($query->tournamentId));
    }
}
