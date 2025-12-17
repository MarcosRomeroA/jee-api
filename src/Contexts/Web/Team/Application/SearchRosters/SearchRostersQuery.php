<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchRosters;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchRostersQuery implements Query
{
    public function __construct(
        public string $teamId,
        public int $limit = 10,
        public int $offset = 0,
    ) {
    }
}
