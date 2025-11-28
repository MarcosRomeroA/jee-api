<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\SearchTeams;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchTeamsController extends ApiController
{
    public function __invoke(Request $request, ?string $sessionId = null): Response
    {
        $input = SearchTeamsRequest::fromHttp($request, $sessionId);
        $this->validateRequest($input);

        $queryObject = $input->toQuery();

        $teamsResponse = $this->queryBus->ask($queryObject);

        return $this->collectionResponse($teamsResponse);
    }
}
