<?php

declare(strict_types=1);

namespace App\Apps\Web\User\WonTournaments;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchUserWonTournamentsController extends ApiController
{
    public function __invoke(
        Request $request,
    ): Response {
        $input = SearchUserWonTournamentsRequest::fromHttp($request);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
