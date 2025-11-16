<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostLikes;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\LikeCollectionResponse;
use App\Contexts\Web\Post\Application\Shared\LikeResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostLikesSearcher
{
    public function __construct(
        private PostRepository $postRepository,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(string $postId, int $limit = 10, int $offset = 0): LikeCollectionResponse
    {
        $post = $this->postRepository->findById(new Uuid($postId));

        $allLikes = $post->getLikes()->toArray();
        $total = count($allLikes);

        // Apply pagination
        $likes = array_slice($allLikes, $offset, $limit);

        $response = [];
        foreach ($likes as $like) {
            $user = $like->getUser();

            $urlProfileImage = null;
            if ($user->getProfileImage()->value() !== null) {
                $urlProfileImage = $this->fileManager->generateTemporaryUrl(
                    'user/profile',
                    $user->getProfileImage()->value()
                );
            }

            $response[] = new LikeResponse(
                $user->getId()->value(),
                $user->getUsername()->value(),
                $user->getFirstname()->value(),
                $user->getLastname()->value(),
                $urlProfileImage,
                $like->getCreatedAt()->value()->format('Y-m-d H:i:s'),
            );
        }

        return new LikeCollectionResponse($response, $limit, $offset, $total);
    }
}
