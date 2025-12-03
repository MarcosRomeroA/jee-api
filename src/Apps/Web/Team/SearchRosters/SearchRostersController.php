<?php declare(strict_types=1);

namespace App\Apps\Web\Team\SearchRosters;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\SearchRosters\SearchRostersQuery;
use Symfony\Component\HttpFoundation\Response;

final class SearchRostersController extends ApiController
{
    public function __invoke(string $teamId): Response
    {
        $query = new SearchRostersQuery($teamId);
        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
