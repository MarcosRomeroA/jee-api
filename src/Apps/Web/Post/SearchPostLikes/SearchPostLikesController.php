<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\SearchPostLikes;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\SearchPostLikes\SearchPostLikesQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchPostLikesController extends ApiController
{
    public function __invoke(Request $request, string $id): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $offset = $request->query->getInt('offset', 0);

        $query = new SearchPostLikesQuery($id, $limit, $offset);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
