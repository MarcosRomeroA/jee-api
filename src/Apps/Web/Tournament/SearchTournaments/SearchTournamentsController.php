<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchTournaments;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchTournamentsController extends ApiController
{
    public function __invoke(Request $request, string $sessionId): Response
    {
        $input = SearchTournamentsRequest::fromHttp($request);
        $this->validateRequest($input);

        $queryObject = $input->toQuery($sessionId);

        $tournamentsResponse = $this->queryBus->ask($queryObject);

        return $this->collectionResponse($tournamentsResponse);
    }
}

