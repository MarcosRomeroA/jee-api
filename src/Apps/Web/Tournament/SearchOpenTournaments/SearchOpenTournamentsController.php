<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchOpenTournaments;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\SearchOpenTournaments\SearchOpenTournamentsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchOpenTournamentsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $query = $request->query->get('q');
        $gameId = $request->query->get('gameId');

        $queryObject = new SearchOpenTournamentsQuery($query, $gameId);

        $tournamentsResponse = $this->queryBus->ask($queryObject);

        return $this->successResponse($tournamentsResponse->toArray());
    }
}

