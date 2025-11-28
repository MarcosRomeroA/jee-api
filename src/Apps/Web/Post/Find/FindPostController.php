<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\Find;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Find\FindPostQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindPostController extends ApiController
{
    public function __invoke(string $id, ?string $sessionId = null): Response
    {
        $query = new FindPostQuery($id, $sessionId);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
