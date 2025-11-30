<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchTournamentsQuery implements Query
{
    public function __construct(
        public array $criteria,
    ) {
    }
}
