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
    public function __invoke(array $ids): array
    {
        $posts = $this->postRepository->findByIds($ids);
        $responses = [];

        foreach ($posts as $post) {
            $resourceUrls = $this->getPostResources->__invoke($post);

            $urlProfileImage = null;
            if ($post->getUser()->getProfileImage()->value() !== null) {
                $urlProfileImage = $this->fileManager->generateTemporaryUrl(
                    'user/profile',
                    $post->getUser()->getProfileImage()->value()
                );
            }

            $sharedPost = null;
            if ($post->getSharedPostId() !== null) {
                $sharedPostEntity = $this->postRepository->findById($post->getSharedPostId());
                $sharedPostResponse = PostResponse::fromEntity($sharedPostEntity);
                $sharedPost = $sharedPostResponse->toArray();
            }

            $sharesQuantity = $this->postRepository->findSharesQuantity($post->getId());

            $responses[] = new PostResponse(
                $post->getId()->value(),
                $post->getBody()->value(),
                $post->getUser()->getUsername()->value(),
                $resourceUrls,
                $post->getCreatedAt()->value()->format('Y-m-d H:i:s'),
                $urlProfileImage,
                $sharedPost,
                $post->getLikes()->count(),
                $sharesQuantity,
                $post->getComments()->count()
            );
        }

        return $responses;
    }
}
