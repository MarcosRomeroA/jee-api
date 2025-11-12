<?php declare(strict_types=1);

namespace App\Apps\Web\Game\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Game\Application\Search\SearchGamesQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchGamesController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $query = $request->query->get('q');

        $queryObject = new SearchGamesQuery($query);

        $gamesResponse = $this->queryBus->ask($queryObject);

        return $this->successResponse($gamesResponse->toArray());
    }
}

