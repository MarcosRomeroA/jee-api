<?php declare(strict_types=1);

namespace App\Apps\Web\Team\SearchMyTeams;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\SearchMyTeams\SearchMyTeamsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchMyTeamsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $query = $request->query->get('q');
        $userId = $this->getAuthenticatedUserId();

        $queryObject = new SearchMyTeamsQuery($userId, $query);

        $teamsResponse = $this->queryBus->ask($queryObject);

        return $this->successResponse($teamsResponse->toArray());
    }
}

