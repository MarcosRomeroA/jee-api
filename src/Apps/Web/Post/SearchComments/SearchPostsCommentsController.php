<?php declare(strict_types=1);

namespace App\Apps\Web\Post\SearchComments;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

class SearchPostsCommentsController extends ApiController
{
    public function __invoke(SearchPostsCommentsRequest $request): Response
    {
        $criteria = $request->get('q') ?? [];

        $query = new SearchPostQuery($criteria);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}