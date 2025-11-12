<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Find;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Player\Application\Find\FindPlayerQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindPlayerController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindPlayerQuery($id);

        $player = $this->queryBus->ask($query);

        return $this->successResponse($player);
    }
}

