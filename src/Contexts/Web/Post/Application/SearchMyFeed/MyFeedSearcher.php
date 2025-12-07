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

            $hasShared = $this->repository->hasUserSharedPost($post->getId(), $userId);
            $post->setHasShared($hasShared);
        }

        $total = $this->repository->countFeed($userId);

        return new PostCollectionResponse($posts, $criteria ?? ["limit" => 0, "offset" => 0], $total, $userId->value());
    }
}
