<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchPostQuery implements Query
{
    public function __construct(
        public ?array $criteria = null
    )
    {
    }
}
