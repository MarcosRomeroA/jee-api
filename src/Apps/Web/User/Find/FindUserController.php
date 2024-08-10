<?php declare(strict_types=1);

namespace App\Apps\Web\User\Find;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Find\FindUserQuery;
use Symfony\Component\HttpFoundation\Response;

class FindUserController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindUserQuery($id);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}