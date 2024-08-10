<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Refresh;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Auth\Application\RefreshToken\GetTokenByRefreshQuery;
use Symfony\Component\HttpFoundation\Response;

class GetTokenByRefreshController extends ApiController
{
    public function __invoke(GetTokenByRefreshRequest $request): Response
    {
        $query = new GetTokenByRefreshQuery(
            $request->refreshToken,
        );

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}