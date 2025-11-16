<?php declare(strict_types=1);

namespace App\Apps\Web\Game\FindAllByGame;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Game\Application\FindAllByGame\FindAllGameRanksByGameQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindAllGameRanksByGameController extends ApiController
{
    public function __invoke(string $gameId): Response
    {
        $query = new FindAllGameRanksByGameQuery($gameId);
        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
