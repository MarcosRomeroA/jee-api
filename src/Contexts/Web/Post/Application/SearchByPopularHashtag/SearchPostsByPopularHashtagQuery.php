<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchByPopularHashtag;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchPostsByPopularHashtagQuery implements Query
{
    public function __construct(
        private string $hashtag,
        private int $page,
        private int $limit,
        private int $days = 30
    ) {
    }

    public function hashtag(): string
    {
        return $this->hashtag;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function days(): int
    {
        return $this->days;
    }
}
