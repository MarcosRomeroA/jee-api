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
            $post->setResourceUrls($this->getPostResources->__invoke($post));
            $post->getUser()->setUrlProfileImage(
                $post->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
            );

            if ($post->getSharedPostId() !== null) {
                try {
                    $sharedPostEntity = $this->postRepository->findById($post->getSharedPostId());
                    $sharedPostEntity->setResourceUrls($this->getPostResources->__invoke($sharedPostEntity));
                    $sharedPostEntity->getUser()->setUrlProfileImage(
                        $sharedPostEntity->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
                    );

                    $post->setSharedPost($sharedPostEntity);
                } catch (\Exception $e) {
                    // If shared post doesn't exist (was deleted), set sharedPost to null
                    $post->setSharedPost(null);
                }
            }

            $sharesQuantity = $this->postRepository->findSharesQuantity($post->getId());
            $post->setSharesQuantity($sharesQuantity);

            $responses[] = PostResponse::fromEntity($post, true, $currentUserId);
        }

        return $responses;
    }
}
