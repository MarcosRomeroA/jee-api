<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchMyTournaments;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchMyTournamentsController extends ApiController
{
    public function __invoke(Request $request, string $sessionId): Response
    {
        $input = SearchMyTournamentsRequest::fromHttp($request, $sessionId);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $tournamentsResponse = $this->queryBus->ask($query);

        return $this->successResponse($tournamentsResponse->toArray());
    }
}

