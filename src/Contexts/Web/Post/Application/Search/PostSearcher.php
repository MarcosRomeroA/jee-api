<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class PostSearcher implements QueryHandler
{
    public function __construct(
        private PostRepository $repository,
        private GetPostResources $getPostResources,
        private string $cdnBaseUrl,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(?array $criteria, ?string $currentUserId = null): PostCollectionResponse
    {
        // Merge default pagination values
        $criteriaWithDefaults = array_merge(
            ["limit" => 0, "offset" => 0],
            $criteria ?? [],
        );

        $posts = $criteria
            ? $this->repository->searchByCriteria($criteria)
            : $this->repository->searchAll();

        foreach ($posts as $post) {
            try {
                // Set default values
                $post->setResourceUrls([]);
                $post->setSharesQuantity(0);
                $post->setHasShared(false);
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
                        $sharedPost = $this->repository->findById(
                            $post->getSharedPostId(),
                        );
                        $sharedPost->setResourceUrls(
                            $this->getPostResources->__invoke($sharedPost),
                        );
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
                    $sharesQuantity = $this->repository->findSharesQuantity(
                        $post->getId(),
                    );
                    $post->setSharesQuantity($sharesQuantity);
                } catch (\Exception $e) {
                    // Keep 0
                }

                // Try to check if user has shared
                if ($currentUserId !== null) {
                    try {
                        $hasShared = $this->repository->hasUserSharedPost(
                            $post->getId(),
                            new \App\Contexts\Shared\Domain\ValueObject\Uuid($currentUserId),
                        );
                        $post->setHasShared($hasShared);
                    } catch (\Exception $e) {
                        // Keep false
                    }
                }
            } catch (\Exception $e) {
                // Log but include the post anyway
                error_log(sprintf(
                    'Failed to fully process post %s: %s',
                    $post->getId()->value(),
                    $e->getMessage()
                ));
            }
        }

        $total = $this->repository->countByCriteria($criteriaWithDefaults);

        return new PostCollectionResponse(
            $posts,
            $criteriaWithDefaults,
            $total,
            $currentUserId,
        );
    }
}
