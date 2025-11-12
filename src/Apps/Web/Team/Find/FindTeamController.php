<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Find;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\Find\FindTeamQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindTeamController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindTeamQuery($id);

        $teamResponse = $this->queryBus->ask($query);

        return $this->successResponse($teamResponse->toArray());
    }
}

