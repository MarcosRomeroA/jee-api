<?php declare(strict_types=1);

namespace App\Apps\Web\Team\SearchRosters;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class SearchRostersController extends ApiController
{
    public function __invoke(string $teamId, SearchRostersRequest $request): Response
    {
        $query = $request->toQuery($teamId);
        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
