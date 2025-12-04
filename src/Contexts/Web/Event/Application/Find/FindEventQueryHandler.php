<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Application\Shared\EventResponse;

final readonly class FindEventQueryHandler implements QueryHandler
{
    public function __construct(
        private EventFinder $finder,
    ) {
    }

    public function __invoke(FindEventQuery $query): EventResponse
    {
        return $this->finder->__invoke(new Uuid($query->id));
    }
}
