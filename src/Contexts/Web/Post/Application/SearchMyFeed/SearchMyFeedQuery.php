<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchMyFeed;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final class SearchMyFeedQuery implements Query
{
    public function __construct(
        public string $id
    )
    {
    }
}