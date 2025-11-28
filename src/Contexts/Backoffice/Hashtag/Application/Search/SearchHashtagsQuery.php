<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchHashtagsQuery implements Query
{
    public function __construct(
        public array $criteria
    ) {
    }
}
