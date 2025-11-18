<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindMatch;

use App\Contexts\Shared\Domain\Bus\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentMatch;

final readonly class FindMatchQueryHandler implements QueryHandler
{
    public function __construct(
        private MatchFinder $matchFinder
    ) {
    }

    public function __invoke(FindMatchQuery $query): TournamentMatch
    {
        return $this->matchFinder->find(new Uuid($query->id));
    }
}
