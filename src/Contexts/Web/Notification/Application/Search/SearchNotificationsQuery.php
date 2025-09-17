<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchNotificationsQuery implements Query
{
    public function __construct(
        public array $criteria
    )
    {
    }
}

