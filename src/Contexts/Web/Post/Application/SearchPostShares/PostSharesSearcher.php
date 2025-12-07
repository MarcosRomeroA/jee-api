<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostShares;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\ShareCollectionResponse;
use App\Contexts\Web\Post\Application\Shared\ShareResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostSharesSearcher
{
    public function __construct(
        private PostRepository $postRepository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(string $postId, int $limit = 10, int $offset = 0): ShareCollectionResponse
    {
        $sharedPosts = $this->postRepository->findSharesByPostId(new Uuid($postId), $limit, $offset);
        $total = $this->postRepository->countSharesByPostId(new Uuid($postId));

        $response = [];
        foreach ($sharedPosts as $sharedPost) {
            $user = $sharedPost->getUser();

            $response[] = new ShareResponse(
                $user->getId()->value(),
                $user->getUsername()->value(),
                $user->getFirstname()->value(),
                $user->getLastname()->value(),
                $user->getAvatarUrl(128, $this->cdnBaseUrl),
                $sharedPost->getCreatedAt()->value()->format('Y-m-d H:i:s'),
            );
        }

        return new ShareCollectionResponse($response, $limit, $offset, $total);
    }
}
