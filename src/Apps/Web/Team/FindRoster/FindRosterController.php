<?php declare(strict_types=1);

namespace App\Apps\Web\Team\FindRoster;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\FindRoster\FindRosterQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindRosterController extends ApiController
{
    public function __invoke(string $teamId, string $rosterId): Response
    {
        $query = new FindRosterQuery($rosterId);

        $rosterResponse = $this->queryBus->ask($query);

        return $this->successResponse($rosterResponse->toArray());
    }
}

