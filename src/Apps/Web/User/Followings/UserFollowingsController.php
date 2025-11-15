<?php declare(strict_types=1);

namespace App\Apps\Web\User\Followings;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Followings\UserFollowingsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserFollowingsController extends ApiController
{
    public function __invoke(
        string $id,
        string $sessionId,
        Request $request,
    ): Response {
        $input = UserFollowingsRequest::fromHttp($id, $sessionId, $request);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
