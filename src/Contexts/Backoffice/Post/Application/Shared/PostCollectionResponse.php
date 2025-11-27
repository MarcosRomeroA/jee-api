<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class PostCollectionResponse extends Response
{
    /**
     * @param PostResponse[] $posts
     */
    public function __construct(
        private readonly array $posts,
        private readonly int $total,
        private readonly int $limit,
        private readonly int $offset,
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn (PostResponse $post) => $post->toArray(), $this->posts),
            'metadata' => [
                'total' => $this->total,
                'count' => count($this->posts),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        ];
    }
}
