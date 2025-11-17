<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\GetPopularHashtags;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class GetPopularHashtagsQuery implements Query
{
    public function __construct(
        private int $days = 30,
        private int $limit = 10
    ) {
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
