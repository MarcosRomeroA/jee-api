<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchByHashtag;

use App\Contexts\Shared\Domain\Bus\Query\QueryHandler;
use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Pagination;
use App\Contexts\Web\Post\Application\Response\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class SearchPostsByHashtagQueryHandler implements QueryHandler
{
    public function __construct(
        private PostRepository $repository
    ) {
    }

    public function __invoke(SearchPostsByHashtagQuery $query): PaginatedResponse
    {
        $pagination = Pagination::fromRequest($query->page(), $query->limit());

        $posts = $this->repository->findByHashtag(
            $query->hashtag(),
            $pagination->limit(),
            $pagination->offset()
        );

        $total = $this->repository->countByHashtag($query->hashtag());

        $postsData = array_map(
            fn($post) => PostResponse::fromEntity($post, true)->toArray(),
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
