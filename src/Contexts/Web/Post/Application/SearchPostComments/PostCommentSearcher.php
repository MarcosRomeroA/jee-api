<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostComments;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostCommentCollectionResponse;
use App\Contexts\Web\Post\Application\Shared\PostCommentResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostCommentSearcher
{
    public function __construct(
        private PostRepository $repository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(string $id, int $limit = 10, int $offset = 0): PostCommentCollectionResponse
    {
        $post = $this->repository->findById(new Uuid($id));

        $allComments = array_filter(
            $post->getComments()->toArray(),
            fn ($comment) => !$comment->isDisabled()
        );
        $total = count($allComments);

        // Apply pagination
        $comments = array_slice($allComments, $offset, $limit);

        $response = [];
        foreach ($comments as $comment) {
            $user = $comment->getUser();

            $response[] = new PostCommentResponse(
                $comment->getId()->value(),
                $comment->getComment()->value(),
                $user->getId()->value(),
                $user->getUsername()->value(),
                $user->getAvatarUrl(64, $this->cdnBaseUrl),
                $comment->getCreatedAt()->value()->format('Y-m-d H:i:s'),
            );
        }

        return new PostCommentCollectionResponse($response, $limit, $offset, $total);
    }
}
