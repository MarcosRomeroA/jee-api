<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\FindPendingRequests;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\FindPendingRequests\FindPendingTournamentRequestsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FindPendingTournamentRequestsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $tournamentId = $request->query->get('tournamentId');

        $query = new FindPendingTournamentRequestsQuery($tournamentId);

        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
