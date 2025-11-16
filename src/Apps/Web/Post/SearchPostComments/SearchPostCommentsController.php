<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\SearchPostComments;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\SearchPostComments\SearchPostCommentsQuery;
use Symfony\Component\HttpFoundation\Response;

class SearchPostCommentsController extends ApiController
{
    public function __invoke(SearchPostCommentsRequest $request, string $id): Response
    {
        $query = new SearchPostCommentsQuery($id, $request->limit, $request->offset);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
