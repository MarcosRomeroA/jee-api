<?php

declare(strict_types=1);

namespace App\Apps\Web\Event\Find;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Event\Application\Find\FindEventQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindEventController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindEventQuery($id);
        $response = $this->ask($query);

        return $this->successResponse($response);
    }
}
