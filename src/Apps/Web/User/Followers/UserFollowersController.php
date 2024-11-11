<?php declare(strict_types=1);

namespace App\Apps\Web\User\Followers;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Followers\UserFollowersQuery;
use Symfony\Component\HttpFoundation\Response;

final class UserFollowersController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $query = new UserFollowersQuery(
            $id,
            $sessionId
        );

        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}