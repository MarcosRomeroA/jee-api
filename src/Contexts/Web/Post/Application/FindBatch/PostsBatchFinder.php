<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\FindBatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostsBatchFinder
{
    public function __construct(
        private PostRepository $postRepository,
        private GetPostResources $getPostResources,
        private string $cdnBaseUrl,
    ) {
    }

    /**
     * @param array<Uuid> $ids
     * @return array<PostResponse>
     */
    public function __invoke(array $ids, ?string $currentUserId = null): array
    {
        $posts = $this->postRepository->findByIds($ids);
        $responses = [];

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
                if ($post->getSharedPostId() !== null) {
                    try {
                        $sharedPostEntity = $this->postRepository->findById($post->getSharedPostId());
                        $sharedPostEntity->setResourceUrls($this->getPostResources->__invoke($sharedPostEntity));
                        $sharedPostEntity->getUser()->setUrlProfileImage(
                            $sharedPostEntity->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
                        );
                        $post->setSharedPost($sharedPostEntity);
                    } catch (\Exception $e) {
                        // Keep null
                    }
                }

                // Try to load shares quantity
                try {
                    $sharesQuantity = $this->postRepository->findSharesQuantity($post->getId());
                    $post->setSharesQuantity($sharesQuantity);
                } catch (\Exception $e) {
                    // Keep 0
                }

                $responses[] = PostResponse::fromEntity($post, true, $currentUserId);
            } catch (\Exception $e) {
                // Log but skip this post if it can't be processed at all
                error_log(sprintf(
                    'Failed to process post %s in batch: %s',
                    $post->getId()->value(),
                    $e->getMessage()
                ));
            }
        }

        return $responses;
    }
}
