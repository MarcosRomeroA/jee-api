<?php declare(strict_types=1);

namespace App\Apps\Web\User\Followings;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Followings\UserFollowingsQuery;
use Symfony\Component\HttpFoundation\Response;

final class UserFollowingsController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $query = new UserFollowingsQuery(
            $id,
            $sessionId
        );

        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}