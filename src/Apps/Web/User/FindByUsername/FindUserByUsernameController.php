<?php declare(strict_types=1);

namespace App\Apps\Web\User\FindByUsername;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\FindUserByUsername\FindUserByUsernameQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindUserByUsernameController extends ApiController
{
    public function __invoke(string $username): Response
    {
        $query = new FindUserByUsernameQuery($username);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}