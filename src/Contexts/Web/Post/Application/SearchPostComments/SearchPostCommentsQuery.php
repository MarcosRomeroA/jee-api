<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostComments;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchPostCommentsQuery implements Query
{
    public function __construct(
        public string $id
    )
    {
    }
}