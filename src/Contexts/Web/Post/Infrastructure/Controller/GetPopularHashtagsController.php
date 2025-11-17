<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Controller;

use App\Contexts\Shared\Domain\CQRS\Query\QueryBus;
use App\Contexts\Web\Post\Application\GetPopularHashtags\GetPopularHashtagsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class GetPopularHashtagsController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    #[Route('/api/post/hashtag/popular', name: 'get_popular_hashtags', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $query = new GetPopularHashtagsQuery(days: 30, limit: 10);

        $response = $this->queryBus->ask($query);

        return new JsonResponse($response->toArray());
    }
}
