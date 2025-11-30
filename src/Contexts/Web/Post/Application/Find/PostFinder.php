<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Find;

use App\Contexts\Shared\Domain\FileManager\FileManager;
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
        private FileManager $fileManager
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Uuid $id, ?string $currentUserId = null): PostResponse
    {
        $post = $this->repository->findById($id);
        $post->setResourceUrls($this->getPostResources->__invoke($post));

        if (!empty($post->getUser()->getProfileImage()->value())) {
            $post->getUser()->setUrlProfileImage(
                $this->fileManager->generateTemporaryUrl('user/profile', $post->getUser()->getProfileImage()->value())
            );
        }

        if ($post->getSharedPostId()) {
            $sharedPost = $this->repository->findById($post->getSharedPostId());
            $sharedPost->setResourceUrls($this->getPostResources->__invoke($sharedPost));

            if (!empty($sharedPost->getUser()->getProfileImage()->value())) {
                $sharedPost->getUser()->setUrlProfileImage(
                    $this->fileManager->generateTemporaryUrl('user/profile', $sharedPost->getUser()->getProfileImage()->value())
                );
            }

            $post->setSharedPost($sharedPost);
        }

        $sharesQuantity = $this->repository->findSharesQuantity($id);
        $post->setSharesQuantity($sharesQuantity);

        return PostResponse::fromEntity($post, true, $currentUserId);
    }
}
