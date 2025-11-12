<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchTeams;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchTeamsQuery implements Query
{
    public function __construct(
        public ?string $query = null
    ) {
    }
}

