<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Admin\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchAdminsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = SearchAdminsRequest::fromHttp($request);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $collection = $this->ask($query);

        return $this->collectionResponse($collection);
    }
}
