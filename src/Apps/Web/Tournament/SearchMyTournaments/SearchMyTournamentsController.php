<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchMyTournaments;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\SearchMyTournaments\SearchMyTournamentsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchMyTournamentsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $query = $request->query->get('q');
        $userId = $this->getAuthenticatedUserId();

        $queryObject = new SearchMyTournamentsQuery($userId, $query);

        $tournamentsResponse = $this->queryBus->ask($queryObject);

        return $this->successResponse($tournamentsResponse->toArray());
    }
}

