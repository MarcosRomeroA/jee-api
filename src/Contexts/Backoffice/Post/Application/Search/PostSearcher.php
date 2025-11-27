<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Search;

use App\Contexts\Backoffice\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Backoffice\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostSearcher
{
    public function __construct(
        private PostRepository $repository
    ) {
    }

    public function __invoke(array $criteria): PostCollectionResponse
    {
        // Backoffice siempre incluye posts deshabilitados
        $criteria['includeDisabled'] = true;

        $posts = $this->repository->searchByCriteria($criteria);
        $total = $this->repository->countByCriteria($criteria);

        $responses = [];
        foreach ($posts as $post) {
            $responses[] = PostResponse::fromEntity($post);
        }

        $limit = $criteria['limit'] ?? 20;
        $offset = $criteria['offset'] ?? 0;

        return new PostCollectionResponse($responses, $total, $limit, $offset);
    }
}
