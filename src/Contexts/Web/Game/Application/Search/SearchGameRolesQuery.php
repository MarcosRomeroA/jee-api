<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchGameRolesQuery implements Query
{
    public function __construct(
        public string $gameId
    ) {
    }
}

