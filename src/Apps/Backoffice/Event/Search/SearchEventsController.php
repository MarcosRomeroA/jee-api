<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Event\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Event\Application\Search\SearchEventsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchEventsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = SearchEventsRequest::fromHttp($request);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $collection = $this->ask($query);

        return $this->collectionResponse($collection);
    }
}
