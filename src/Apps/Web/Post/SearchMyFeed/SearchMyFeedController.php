<?php declare(strict_types=1);

namespace App\Apps\Web\Post\SearchMyFeed;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\SearchMyFeed\SearchMyFeedQuery;
use Symfony\Component\HttpFoundation\Response;

final class SearchMyFeedController extends ApiController
{
    public function __invoke(SearchMyFeedRequest $request, string $sessionId): Response
    {
        $criteria = $request->q ?? null;

        $query = new SearchMyFeedQuery($sessionId, $criteria);

        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}