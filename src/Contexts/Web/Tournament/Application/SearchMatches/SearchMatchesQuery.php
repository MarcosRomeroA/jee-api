<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchMatches;

use App\Contexts\Shared\Domain\Bus\Query\Query;

final readonly class SearchMatchesQuery implements Query
{
    public function __construct(
        public string $tournamentId,
        public ?int $round = null
    ) {
    }
}
