<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Team\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchTeamsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = SearchTeamsRequest::fromHttp($request);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $response = $this->ask($query);

        return $this->collectionResponse($response);
    }
}
