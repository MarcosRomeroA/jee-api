<?php declare(strict_types=1);

namespace App\Apps\Web\Game\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Game\Application\Search\SearchGameRanksQuery;
use Symfony\Component\HttpFoundation\Response;

final class SearchGameRanksController extends ApiController
{
    public function __invoke(string $gameId): Response
    {
        $query = new SearchGameRanksQuery($gameId);
        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}

