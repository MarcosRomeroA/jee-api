<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\FindPendingRequests;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FindPendingTeamRequestsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = FindPendingTeamRequestsRequest::fromHttp($request);
        $this->validateRequest($input);

        $queryObject = $input->toQuery();

        $requestsResponse = $this->queryBus->ask($queryObject);

        return $this->collectionResponse($requestsResponse);
    }
}
