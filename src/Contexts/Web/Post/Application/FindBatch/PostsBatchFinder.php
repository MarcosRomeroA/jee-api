<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\FindBatch;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostsBatchFinder
{
    public function __construct(
        private PostRepository $postRepository,
        private GetPostResources $getPostResources,
        private FileManager $fileManager,
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

            if ($post->getUser()->getProfileImage()->value() !== null) {
                $post->getUser()->setUrlProfileImage(
                    $this->fileManager->generateTemporaryUrl(
                        'user/profile',
                        $post->getUser()->getProfileImage()->value()
                    )
                );
            }

            if ($post->getSharedPostId() !== null) {
                $sharedPostEntity = $this->postRepository->findById($post->getSharedPostId());
                $sharedPostEntity->setResourceUrls($this->getPostResources->__invoke($sharedPostEntity));

                if (!empty($sharedPostEntity->getUser()->getProfileImage()->value())) {
                    $sharedPostEntity->getUser()->setUrlProfileImage(
                        $this->fileManager->generateTemporaryUrl(
                            'user/profile',
                            $sharedPostEntity->getUser()->getProfileImage()->value()
                        )
                    );
                }

                $post->setSharedPost($sharedPostEntity);
            }

            $sharesQuantity = $this->postRepository->findSharesQuantity($post->getId());
            $post->setSharesQuantity($sharesQuantity);

            $responses[] = PostResponse::fromEntity($post, true, $currentUserId);
        }

        return $responses;
    }
}
