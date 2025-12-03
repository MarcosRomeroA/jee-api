<?php declare(strict_types=1);

namespace App\Apps\Web\Team\SearchRosterPlayers;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\SearchRosterPlayers\SearchRosterPlayersQuery;
use Symfony\Component\HttpFoundation\Response;

final class SearchRosterPlayersController extends ApiController
{
    public function __invoke(string $teamId, string $rosterId): Response
    {
        $query = new SearchRosterPlayersQuery($rosterId, $teamId);
        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
