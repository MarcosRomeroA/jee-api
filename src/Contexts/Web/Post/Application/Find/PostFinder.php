<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Find;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class PostFinder
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
    public function __invoke(Uuid $id, ?string $currentUserId = null): PostResponse
    {
        $post = $this->repository->findById($id);
        $post->setResourceUrls($this->getPostResources->__invoke($post));
        $post->getUser()->setUrlProfileImage(
            $post->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
        );

        if ($post->getSharedPostId()) {
            try {
                $sharedPost = $this->repository->findById($post->getSharedPostId());
                $sharedPost->setResourceUrls($this->getPostResources->__invoke($sharedPost));
                $sharedPost->getUser()->setUrlProfileImage(
                    $sharedPost->getUser()->getAvatarUrl(128, $this->cdnBaseUrl)
                );

                $post->setSharedPost($sharedPost);
            } catch (\Exception $e) {
                // If shared post doesn't exist (was deleted), set sharedPost to null
                $post->setSharedPost(null);
            }
        }

        $sharesQuantity = $this->repository->findSharesQuantity($id);
        $post->setSharesQuantity($sharesQuantity);

        return PostResponse::fromEntity($post, true, $currentUserId);
    }
}
