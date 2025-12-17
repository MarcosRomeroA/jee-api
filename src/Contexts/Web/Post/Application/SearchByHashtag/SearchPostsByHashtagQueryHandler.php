<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchByHashtag;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Pagination;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class SearchPostsByHashtagQueryHandler implements QueryHandler
{
    public function __construct(
        private PostRepository $repository,
        private GetPostResources $getPostResources,
        private string $cdnBaseUrl,
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

        foreach ($posts as $post) {
            try {
                // Set defaults
                $post->setResourceUrls([]);
                $post->setSharesQuantity(0);
                $post->setSharedPost(null);

                // Try to load resources
                try {
                    $post->setResourceUrls($this->getPostResources->__invoke($post));
                } catch (\Exception $e) {
                    // Keep empty array
                }

                // Try to load user avatar
                try {
                    $post->getUser()->setUrlProfileImage(
                        $post->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
                    );
                } catch (\Exception $e) {
                    // Continue without avatar
                }

                // Try to load shared post
                if ($post->getSharedPostId()) {
                    try {
                        $sharedPost = $this->repository->findById($post->getSharedPostId());
                        $sharedPost->setResourceUrls($this->getPostResources->__invoke($sharedPost));
                        $sharedPost->getUser()->setUrlProfileImage(
                            $sharedPost->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
                        );
                        $post->setSharedPost($sharedPost);
                    } catch (\Exception $e) {
                        // Keep null
                    }
                }

                // Try to load shares quantity
                try {
                    $sharesQuantity = $this->repository->findSharesQuantity($post->getId());
                    $post->setSharesQuantity($sharesQuantity);
                } catch (\Exception $e) {
                    // Keep 0
                }
            } catch (\Exception $e) {
                // Log but include post anyway
                error_log(sprintf(
                    'Failed to fully process post %s in hashtag search: %s',
                    $post->getId()->value(),
                    $e->getMessage()
                ));
            }
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
