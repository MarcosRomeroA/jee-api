<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchRosters;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\RosterCollectionResponse;

final readonly class SearchRostersQueryHandler implements QueryHandler
{
    public function __construct(private RostersSearcher $searcher)
    {
    }

    public function __invoke(SearchRostersQuery $query): RosterCollectionResponse
    {
        return $this->searcher->__invoke(
            new Uuid($query->teamId),
            $query->limit,
            $query->offset,
        );
    }
}
