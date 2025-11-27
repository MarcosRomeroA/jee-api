<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchPostsQuery implements Query
{
    public function __construct(
        public array $criteria
    ) {
    }
}
