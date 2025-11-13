<?php declare(strict_types=1);

namespace App\Apps\Web\Team\SearchMyTeams;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchMyTeamsController extends ApiController
{
    public function __invoke(Request $request, string $sessionId): Response
    {
        $input = SearchMyTeamsRequest::fromHttp($request, $sessionId);

        $this->validateRequest($input);

        $queryObject = $input->toQuery();

        $teamsResponse = $this->queryBus->ask($queryObject);

        return $this->collectionResponse($teamsResponse);
    }
}

