<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchByPopularHashtag;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Pagination;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class SearchPostsByPopularHashtagQueryHandler implements QueryHandler
{
    public function __construct(
        private PostRepository $repository,
        private GetPostResources $getPostResources,
        private string $cdnBaseUrl,
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

        foreach ($posts as $post) {
            $post->setResourceUrls($this->getPostResources->__invoke($post));
            $post->getUser()->setUrlProfileImage(
                $post->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
            );

            if ($post->getSharedPostId()) {
                $sharedPost = $this->repository->findById($post->getSharedPostId());
                $sharedPost->setResourceUrls($this->getPostResources->__invoke($sharedPost));
                $sharedPost->getUser()->setUrlProfileImage(
                    $sharedPost->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
                );
                $post->setSharedPost($sharedPost);
            }

            $sharesQuantity = $this->repository->findSharesQuantity($post->getId());
            $post->setSharesQuantity($sharesQuantity);
        }

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
