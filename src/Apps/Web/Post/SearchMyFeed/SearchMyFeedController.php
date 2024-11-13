<?php declare(strict_types=1);

namespace App\Apps\Web\Post\SearchMyFeed;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\SearchMyFeed\SearchMyFeedQuery;
use Symfony\Component\HttpFoundation\Response;

final class SearchMyFeedController extends ApiController
{
    public function __invoke(string $sessionId): Response
    {
        $query = new SearchMyFeedQuery($sessionId);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}