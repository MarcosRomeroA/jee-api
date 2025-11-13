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
        $q = $request->query->get('q');

        $query = new SearchGamesQuery($q);
        $response = $this->ask($query);

        // collectionResponse ya serializa usando ->toArray()
        return $this->collectionResponse($response);
    }
}

