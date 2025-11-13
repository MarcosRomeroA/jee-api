<?php declare(strict_types=1);

namespace App\Apps\Web\Game\Find;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Game\Application\Find\FindGameQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindGameController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindGameQuery($id);
        $response = $this->ask($query);

        return $this->successResponse($response?->toArray());
    }
}

