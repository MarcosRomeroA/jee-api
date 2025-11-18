<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchMatches;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\SearchMatches\SearchMatchesQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchMatchesController extends ApiController
{
    public function __invoke(string $tournamentId, Request $request): Response
    {
        $input = SearchMatchesRequest::fromHttp($request, $tournamentId);
        $this->validateRequest($input);

        $query = new SearchMatchesQuery(
            $input->tournamentId,
            $input->round
        );

        $matches = $this->queryBus->ask($query);

        $result = [];
        foreach ($matches as $match) {
            // ...existing code...
        }

        return new JsonResponse($result);
    }
}
