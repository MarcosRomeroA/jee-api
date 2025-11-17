<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchByPopularHashtag;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Pagination;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class SearchPostsByPopularHashtagQueryHandler implements QueryHandler
{
    public function __construct(
        private PostRepository $repository
    ) {
    }

    public function __invoke(SearchPostsByPopularHashtagQuery $query): PaginatedResponse
    {
        $pagination = Pagination::fromRequest($query->page(), $query->limit());

        $posts = $this->repository->findByPopularHashtag(
            $query->hashtag(),
            $query->days(),
            $pagination->limit(),
            $pagination->offset()
        );

        $total = $this->repository->countByPopularHashtag(
            $query->hashtag(),
            $query->days()
        );

        $postsData = array_map(
            fn ($post) => PostResponse::fromEntity($post, true)->toArray(),
            $posts
        );

        return PaginatedResponse::create(
            $postsData,
            $pagination->page(),
            $total,
            $pagination->limit()
        );
    }
}
