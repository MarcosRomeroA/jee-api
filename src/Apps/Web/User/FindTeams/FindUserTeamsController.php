<?php declare(strict_types=1);

namespace App\Apps\Web\User\FindTeams;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\FindTeams\FindUserTeamsQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindUserTeamsController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindUserTeamsQuery($id);
        $teams = $this->queryBus->ask($query);

        return $this->successResponse($teams);
    }
}

