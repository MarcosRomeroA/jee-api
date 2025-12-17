<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchMyFeed;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class MyFeedSearcher
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
    public function __invoke(Uuid $userId, ?array $criteria): PostCollectionResponse
    {
        $posts = $this->repository->searchFeed($userId, $criteria);

        foreach ($posts as $post) {
            try {
                // Set default values in case of any error
                $post->setResourceUrls([]);
                $post->setSharesQuantity(0);
                $post->setHasShared(false);
                $post->setSharedPost(null);

                // Try to load resources
                try {
                    $post->setResourceUrls($this->getPostResources->__invoke($post));
                } catch (\Exception $e) {
                    // Keep empty array as default
                }

                // Try to load user profile image
                try {
                    $post->getUser()->setUrlProfileImage(
                        $post->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
                    );
                } catch (\Exception $e) {
                    // User avatar loading failed, continue without it
                }

                // Try to load shared post if exists
                if ($post->getSharedPostId()) {
                    try {
                        $sharedPost = $this->repository->findById($post->getSharedPostId());
                        $sharedPost->setResourceUrls($this->getPostResources->__invoke($sharedPost));
                        $sharedPost->getUser()->setUrlProfileImage(
                            $sharedPost->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
                        );
                        $post->setSharedPost($sharedPost);
                    } catch (\Exception $e) {
                        // Shared post doesn't exist or failed to load, keep null
                    }
                }

                // Try to load shares quantity
                try {
                    $sharesQuantity = $this->repository->findSharesQuantity($post->getId());
                    $post->setSharesQuantity($sharesQuantity);
                } catch (\Exception $e) {
                    // Keep 0 as default
                }

                // Try to check if user has shared
                try {
                    $hasShared = $this->repository->hasUserSharedPost($post->getId(), $userId);
                    $post->setHasShared($hasShared);
                } catch (\Exception $e) {
                    // Keep false as default
                }
            } catch (\Exception $e) {
                // Even if everything fails, include the post with minimal data
                // This ensures the count matches the requested limit
                error_log(sprintf(
                    'Failed to fully process post %s in feed: %s',
                    $post->getId()->value(),
                    $e->getMessage()
                ));
            }
        }

        $total = $this->repository->countFeed($userId);

        // Ensure criteria has proper default values
        $responseCriteria = $criteria ?? ["limit" => 10, "offset" => 0];
        if (!isset($responseCriteria["limit"])) {
            $responseCriteria["limit"] = 10;
        }
        if (!isset($responseCriteria["offset"])) {
            $responseCriteria["offset"] = 0;
        }

        return new PostCollectionResponse($posts, $responseCriteria, $total, $userId->value());
    }
}
