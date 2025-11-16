<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostShares;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchPostSharesQuery implements Query
{
    public function __construct(
        public string $postId,
        public ?int $limit = 10,
        public ?int $offset = 0,
    ) {
    }
}
