<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Player\Application\Search\SearchPlayersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchPlayersController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $query = new SearchPlayersQuery(
            $request->query->get('q'),
            $request->query->get('gameId'),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );

        $response = $this->queryBus->ask($query);

        return $this->json($response->toArray());
    }
}

