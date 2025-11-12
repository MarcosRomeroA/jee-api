<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchMyTeams;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchMyTeamsQuery implements Query
{
    public function __construct(
        public string $ownerId,
        public ?string $query = null
    ) {
    }
}

