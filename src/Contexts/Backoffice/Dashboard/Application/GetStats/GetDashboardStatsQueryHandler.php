<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Dashboard\Application\GetStats;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class GetDashboardStatsQueryHandler implements QueryHandler
{
    public function __construct(
        private DashboardStatsSearcher $searcher,
    ) {
    }

    public function __invoke(GetDashboardStatsQuery $query): DashboardStatsResponse
    {
        return $this->searcher->__invoke();
    }
}
