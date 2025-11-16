<?php declare(strict_types=1);

namespace App\Apps\Web\Game\FindAllByGame;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Game\Application\FindAllByGame\FindAllGameRolesByGameQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindAllGameRolesByGameController extends ApiController
{
    public function __invoke(string $gameId): Response
    {
        $query = new FindAllGameRolesByGameQuery($gameId);
        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
