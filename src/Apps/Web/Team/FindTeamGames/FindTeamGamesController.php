<?php declare(strict_types=1);

namespace App\Apps\Web\Team\FindTeamGames;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\FindTeamGames\FindTeamGamesQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindTeamGamesController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindTeamGamesQuery($id);
        
        $teamGamesResponse = $this->queryBus->ask($query);

        return $this->successResponse($teamGamesResponse->toArray());
    }
}
