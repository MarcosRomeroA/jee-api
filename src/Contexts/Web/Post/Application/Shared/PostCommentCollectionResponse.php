<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

class PostCommentCollectionResponse extends Response
{
    /**
     * @param array<PostCommentResponse> $comments
     */
    public function __construct(
        private readonly array $comments,
        private readonly int $limit = 10,
        private readonly int $offset = 0,
        private readonly int $total = 0,
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            fn (PostCommentResponse $comment) => $comment->toArray(),
            $this->comments
        );

        return [
            'data' => $data,
            'metadata' => [
                'limit' => $this->limit,
                'offset' => $this->offset,
                'total' => $this->total,
                'count' => count($data),
            ]
        ];
    }
}
