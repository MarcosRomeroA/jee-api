<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\SearchPostShares;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\SearchPostShares\SearchPostSharesQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchPostSharesController extends ApiController
{
    public function __invoke(Request $request, string $id): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $offset = $request->query->getInt('offset', 0);

        $query = new SearchPostSharesQuery($id, $limit, $offset);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
