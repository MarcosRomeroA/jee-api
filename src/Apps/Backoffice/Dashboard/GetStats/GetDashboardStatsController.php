<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Dashboard\GetStats;

use App\Contexts\Backoffice\Dashboard\Application\GetStats\GetDashboardStatsQuery;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class GetDashboardStatsController extends ApiController
{
    public function __invoke(): Response
    {
        $query = new GetDashboardStatsQuery();
        $response = $this->ask($query);

        return $this->successResponse($response);
    }
}
