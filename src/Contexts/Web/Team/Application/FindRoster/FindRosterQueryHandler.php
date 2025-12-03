<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindRoster;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\RosterResponse;

final readonly class FindRosterQueryHandler implements QueryHandler
{
    public function __construct(private RosterFinder $finder)
    {
    }

    public function __invoke(FindRosterQuery $query): RosterResponse
    {
        return $this->finder->__invoke(
            new Uuid($query->rosterId),
        );
    }
}

