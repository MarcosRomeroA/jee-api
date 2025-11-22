<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchPostsController extends ApiController
{
    public function __invoke(Request $request, ?string $sessionId = null): Response
    {
        $input = SearchPostsRequest::fromHttp($request, $sessionId);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
