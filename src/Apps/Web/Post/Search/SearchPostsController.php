<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Search\SearchPostQuery;
use Symfony\Component\HttpFoundation\Response;

class SearchPostsController extends ApiController
{
    public function __invoke(SearchPostsRequest $request): Response
    {
        $criteria = $request->q;

        $query = new SearchPostQuery($criteria);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}