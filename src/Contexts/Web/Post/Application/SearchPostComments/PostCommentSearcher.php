<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostComments;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostCommentCollectionResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostCommentSearcher implements QueryHandler
{
    public function __construct(
        private PostRepository $repository,
    ) {
    }

    public function __invoke(string $id, int $limit = 10, int $offset = 0): PostCommentCollectionResponse
    {
        $post = $this->repository->findById(new Uuid($id));

        $allComments = $post->getComments()->toArray();
        $total = count($allComments);

        // Apply pagination
        $comments = array_slice($allComments, $offset, $limit);

        return new PostCommentCollectionResponse($comments, $limit, $offset, $total);
    }
}
