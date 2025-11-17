<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchByHashtag;

use App\Contexts\Shared\Domain\Bus\Query\Query;

final readonly class SearchPostsByHashtagQuery implements Query
{
    public function __construct(
        private string $hashtag,
        private int $page,
        private int $limit
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
}
