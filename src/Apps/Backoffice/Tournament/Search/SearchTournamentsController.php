<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Tournament\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchTournamentsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = SearchTournamentsRequest::fromHttp($request);
        $query = $input->toQuery();
        $collection = $this->ask($query);

        return $this->collectionResponse($collection);
    }
}
