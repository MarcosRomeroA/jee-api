<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\FindBatch;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindPostsByIdsQuery implements Query
{
    /**
     * @param array<string> $ids
     */
    public function __construct(
        public array $ids,
        public ?string $currentUserId = null,
    ) {
    }
}
